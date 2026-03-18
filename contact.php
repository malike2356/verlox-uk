<?php
// Verlox UK contact handler.
// Sends form submissions to contact@verlox.uk with layered anti-spam protections.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

function verlox_client_ip(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Take first value (left-most) from XFF: client, proxy1, proxy2...
        $parts = explode(',', (string)$_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($parts[0] ?? $ip);
    }
    return $ip;
}

function verlox_is_spam_message(string $message): bool {
    $m = mb_strtolower($message);

    // Honeypot/keyword style filters (tuned to reduce obvious automated spam).
    $keywords = [
        'viagra', 'casino', 'crypto', 'bitcoin', 'free money', 'work from home',
        'earn $', 'unlimited subscribers', 'porn', 'walmart', 'forex'
    ];
    foreach ($keywords as $k) {
        if (strpos($m, $k) !== false) return true;
    }

    // If someone includes a lot of links, it's often promotional spam.
    $urlCount = preg_match_all('/https?:\\/\\//i', $message, $dummy) ?: 0;
    $wwwCount = preg_match_all('/\\bwww\\./i', $message, $dummy) ?: 0;
    if (($urlCount + $wwwCount) > 3) return true;

    return false;
}

function verlox_rate_limit(string $ip): bool {
    $maxCount = 2;       // max attempts
    $windowSeconds = 600; // per 10 minutes

    $now = time();
    $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'verlox_contact_rl';
    $key = hash('sha256', $ip ?: 'unknown');
    $file = $dir . DIRECTORY_SEPARATOR . $key . '.json';

    // If we can't write rate-limit files, fail open (don't block real users).
    if (!@mkdir($dir, 0700, true) && !is_dir($dir)) {
        return true;
    }

    $raw = @file_get_contents($file);
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

$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$botField = trim((string)($_POST['company'] ?? '')); // honeypot
$formTs = (string)($_POST['form_ts'] ?? '');

// Basic header injection guard.
if (preg_match('/[\\r\\n]/', $name) || preg_match('/[\\r\\n]/', $email) || preg_match('/[\\r\\n]/', $message)) {
    http_response_code(400);
    echo 'Invalid form submission.';
    exit;
}

// Honeypot: bots often fill hidden fields.
if ($botField !== '') {
    http_response_code(400);
    echo 'Invalid form submission.';
    exit;
}

// Rate-limit by IP.
$ip = verlox_client_ip();
if (!verlox_rate_limit($ip)) {
    http_response_code(429);
    echo 'Too many requests. Please wait a moment.';
    exit;
}

// Timestamp validation: block very old or missing timestamps.
$tsInt = (int)$formTs;
// We expect ms since epoch (from JS).
if ($tsInt > 1000000000000) {
    $submittedAt = intdiv($tsInt, 1000);
} else {
    $submittedAt = $tsInt > 0 ? $tsInt : 0;
}
$age = $submittedAt > 0 ? (time() - $submittedAt) : PHP_INT_MAX;
if ($submittedAt === 0 || $age > 900) { // 15 minutes max age
    http_response_code(400);
    echo 'Invalid form submission.';
    exit;
}

// Validate inputs.
if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Invalid form submission.';
    exit;
}

$name = mb_substr($name, 0, 80);
$email = mb_substr($email, 0, 254);
$message = mb_substr($message, 0, 2000);

if (mb_strlen($name) < 2 || mb_strlen($message) < 10) {
    http_response_code(400);
    echo 'Invalid form submission.';
    exit;
}

if (verlox_is_spam_message($message)) {
    http_response_code(400);
    echo 'Invalid form submission.';
    exit;
}

$to = 'contact@verlox.uk';
$subject = 'Verlox UK enquiry from ' . $name;

$body = "New enquiry from Verlox UK website:\n\n";
$body .= "Name: {$name}\n";
$body .= "Email: {$email}\n\n";
$body .= "Message:\n{$message}\n";

$headers = [];
$headers[] = 'From: Verlox UK Website <no-reply@verlox.uk>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: PHP/' . phpversion();

@mail($to, $subject, $body, implode("\r\n", $headers));

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Thanks — Verlox UK</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="refresh" content="5;url=/">
    <style>
        body { margin: 0; font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background:#020617; color:#e5e7eb; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .wrap { max-width:420px; padding:24px; border-radius:16px; background:rgba(15,23,42,.9); border:1px solid rgba(148,163,184,.3); text-align:center; }
        h1 { margin:0 0 10px; font-size:22px; }
        p  { margin:6px 0; font-size:14px; line-height:1.6; color:#cbd5f5; }
        a  { color:#eab308; text-decoration:none; }
        a:hover{ text-decoration:underline; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Thanks for getting in touch</h1>
        <p>We’ve received your message and will reply from <strong>contact@verlox.uk</strong>.</p>
        <p>You’ll be redirected back to the homepage in a few seconds, or you can <a href="/">return now</a>.</p>
    </div>
</body>
</html>

