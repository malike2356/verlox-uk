<?php
// Verlox UK contact handler.
// Sends form submissions to contact@verlox.uk with layered anti-spam protections.
// Supports both standard HTML response and JSON (AJAX) via Accept: application/json.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo 'Method Not Allowed';
    exit;
}

$wants_json = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

function verlox_json_error(int $code, string $msg): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'message' => $msg]);
    exit;
}

function verlox_fail(int $code, string $html_msg, string $json_msg, bool $wants_json): never {
    if ($wants_json) {
        verlox_json_error($code, $json_msg);
    }
    http_response_code($code);
    echo $html_msg;
    exit;
}

function verlox_client_ip(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', (string)$_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($parts[0] ?? $ip);
    }
    return $ip;
}

function verlox_is_spam_message(string $message): bool {
    $m = mb_strtolower($message);
    $keywords = [
        'viagra', 'casino', 'crypto', 'bitcoin', 'free money', 'work from home',
        'earn $', 'unlimited subscribers', 'porn', 'walmart', 'forex',
    ];
    foreach ($keywords as $k) {
        if (str_contains($m, $k)) return true;
    }
    $urlCount  = preg_match_all('/https?:\\/\\//i', $message, $dummy) ?: 0;
    $wwwCount  = preg_match_all('/\\bwww\\./i',     $message, $dummy) ?: 0;
    return ($urlCount + $wwwCount) > 3;
}

function verlox_rate_limit(string $ip): bool {
    $maxCount      = 2;
    $windowSeconds = 600;
    $now = time();
    $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'verlox_contact_rl';
    $key  = hash('sha256', $ip ?: 'unknown');
    $file = $dir . DIRECTORY_SEPARATOR . $key . '.json';

    if (!@mkdir($dir, 0700, true) && !is_dir($dir)) {
        return true; // fail open — don't block real users
    }

    // Opportunistic cleanup: remove files not touched in 2× the window
    $cleanupChance = rand(1, 20);
    if ($cleanupChance === 1 && is_dir($dir)) {
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*.json') ?: [] as $f) {
            if (@filemtime($f) < ($now - $windowSeconds * 2)) {
                @unlink($f);
            }
        }
    }

    $raw   = @file_get_contents($file);
    $times = [];
    if (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            foreach ($decoded as $t) {
                $t = (int)$t;
                if ($t > ($now - $windowSeconds)) $times[] = $t;
            }
        }
    }

    if (count($times) >= $maxCount) {
        return false;
    }

    $times[] = $now;
    @file_put_contents($file, json_encode($times), LOCK_EX);
    return true;
}

function verlox_log(string $level, string $msg): void {
    $line = '[' . date('Y-m-d H:i:s') . '] [' . $level . '] ' . $msg . PHP_EOL;
    $log  = __DIR__ . '/contact.log';
    @file_put_contents($log, $line, FILE_APPEND | LOCK_EX);
}

// ── Inputs ────────────────────────────────────────────────────────────────────
$name     = trim((string)($_POST['name']     ?? ''));
$email    = trim((string)($_POST['email']    ?? ''));
$message  = trim((string)($_POST['message']  ?? ''));
$botField = trim((string)($_POST['company']  ?? '')); // honeypot
$formTs   = (string)($_POST['form_ts'] ?? '');

// Header injection guard
if (preg_match('/[\\r\\n]/', $name) || preg_match('/[\\r\\n]/', $email) || preg_match('/[\\r\\n]/', $message)) {
    verlox_fail(400, 'Invalid form submission.', 'Invalid form submission.', $wants_json);
}

// Honeypot
if ($botField !== '') {
    verlox_fail(400, 'Invalid form submission.', 'Invalid form submission.', $wants_json);
}

// Rate limit
$ip = verlox_client_ip();
if (!verlox_rate_limit($ip)) {
    verlox_fail(429, 'Too many requests. Please wait a moment.', 'Too many requests. Please wait a moment.', $wants_json);
}

// Timestamp validation
$tsInt = (int)$formTs;
$submittedAt = ($tsInt > 1_000_000_000_000) ? intdiv($tsInt, 1000) : ($tsInt > 0 ? $tsInt : 0);
$age = $submittedAt > 0 ? (time() - $submittedAt) : PHP_INT_MAX;
if ($submittedAt === 0 || $age > 900) {
    verlox_fail(400, 'Invalid form submission.', 'Invalid form submission.', $wants_json);
}

// Validate
if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    verlox_fail(400, 'Invalid form submission.', 'Please fill in all fields with a valid email address.', $wants_json);
}

$name    = mb_substr($name,    0, 80);
$email   = mb_substr($email,   0, 254);
$message = mb_substr($message, 0, 2000);

if (mb_strlen($name) < 2 || mb_strlen($message) < 10) {
    verlox_fail(400, 'Invalid form submission.', 'Name or message is too short.', $wants_json);
}

if (verlox_is_spam_message($message)) {
    verlox_fail(400, 'Invalid form submission.', 'Invalid form submission.', $wants_json);
}

// ── Send ──────────────────────────────────────────────────────────────────────
$to      = 'contact@verlox.uk';
$subject = 'Verlox UK enquiry from ' . $name;

$body  = "New enquiry from the Verlox UK website:\n\n";
$body .= "Name:    {$name}\n";
$body .= "Email:   {$email}\n";
$body .= "IP:      {$ip}\n\n";
$body .= "Message:\n{$message}\n";

$headers   = [];
$headers[] = 'From: Verlox UK Website <no-reply@verlox.uk>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: PHP/' . phpversion();

$sent = mail($to, $subject, $body, implode("\r\n", $headers));

if (!$sent) {
    verlox_log('ERROR', "mail() failed for enquiry from {$ip} <{$email}>");
    verlox_fail(500, 'Sorry, your message could not be delivered. Please email contact@verlox.uk directly.', 'Mail delivery failed. Please email contact@verlox.uk directly.', $wants_json);
}

verlox_log('INFO', "Enquiry sent from {$ip} <{$email}>");

// ── Response ──────────────────────────────────────────────────────────────────
if ($wants_json) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'message' => 'Message sent']);
    exit;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Thanks — Verlox UK</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="refresh" content="5;url=/">
    <style>
        body{margin:0;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#0B1829;color:#e5e7eb;display:flex;align-items:center;justify-content:center;min-height:100vh}
        .wrap{max-width:420px;padding:32px;border-radius:20px;background:#0F223C;border:1px solid rgba(201,168,76,.3);text-align:center}
        h1{margin:0 0 12px;font-size:24px}
        p{margin:8px 0;font-size:14px;line-height:1.65;color:#B8C0D8}
        a{color:#C9A84C;text-decoration:none}
        a:hover{text-decoration:underline}
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Thanks for getting in touch</h1>
        <p>We've received your message and will reply from <strong>contact@verlox.uk</strong>.</p>
        <p>Redirecting you back in a few seconds, or <a href="/">return now</a>.</p>
    </div>
</body>
</html>
