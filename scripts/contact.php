<?php
// Simple contact handler for Verlox UK.
// Sends form submissions to contact@verlox.uk.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Invalid form submission.';
    exit;
}

$to      = 'contact@verlox.uk';
$subject = 'Verlox UK enquiry from ' . $name;

// Build a simple plain-text email body.
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

