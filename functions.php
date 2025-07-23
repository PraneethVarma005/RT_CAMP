<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    //TODO: Implement this function
    return (string) rand(100000, 999999);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    // TODO: Implement this function
    $to = $email;
    $subject = "Your Verification Code";
    $message = "Your verification code is: $code";
    $headers = "From: no-reply@example.com";

    return mail($to, $subject, $message, $headers);
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    // TODO: Implement this function
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    if (!in_array($email, $emails)) {
        return file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
    }
    return true;
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    // TODO: Implement this function
    if (!file_exists($file)) return false;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $updatedEmails = array_filter($emails, fn($e) => trim($e) !== $email);

    return file_put_contents($file, implode(PHP_EOL, $updatedEmails) . PHP_EOL, LOCK_EX) !== false;
}

/**
 * Fetch GitHub timeline.
 */
function fetchGitHubTimeline() {
    // TODO: Implement this function
    $url = "https://api.github.com/users/octocat/events";
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => [
                "User-Agent: PHP"
            ]
        ]
    ];
    $context = stream_context_create($opts);
    $json = file_get_contents($url, false, $context);
    return json_decode($json, true);
}

/**
 * Format GitHub timeline data. Returns a valid HTML sting.
 */
function formatGitHubData(array $data): string {
    // TODO: Implement this function
    $html = "<h2>GitHub Timeline:</h2><ul>";
    foreach (array_slice($data, 0, 5) as $event) {
        $type = htmlspecialchars($event['type']);
        $repo = htmlspecialchars($event['repo']['name']);
        $html .= "<li><strong>$type</strong> on <em>$repo</em></li>";
    }
    $html .= "</ul>";
    return $html;
}

/**
 * Send the formatted GitHub updates to registered emails.
 */
function sendGitHubUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    // TODO: Implement this function
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = fetchGitHubTimeline();
    $message = formatGitHubData($data);

    foreach ($emails as $email) {
        mail($email, "GitHub Updates", $message, "Content-type: text/html; charset=UTF-8\r\nFrom: no-reply@example.com");
    }
}
