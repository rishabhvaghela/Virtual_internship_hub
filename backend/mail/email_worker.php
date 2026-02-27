<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../mail/send_mail.php';

ignore_user_abort(true);
set_time_limit(0);

try {

    $stmt = $pdo->prepare("
        SELECT id, to_email, subject, body
        FROM email_queue
        WHERE status='pending'
        ORDER BY id ASC
        LIMIT 5
    ");

    $stmt->execute();

    $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$emails) {
        echo "No pending emails";
        exit;
    }

    foreach ($emails as $email) {

        // ✅ USE EXISTING FUNCTION
        $sent = sendRawEmail(
            $email['to_email'],
            $email['subject'],
            $email['body']
        );

        if ($sent) {

            $update = $pdo->prepare("
                UPDATE email_queue
                SET status='sent', sent_at=NOW()
                WHERE id=?
            ");

            $update->execute([$email['id']]);

        } else {

            $update = $pdo->prepare("
                UPDATE email_queue
                SET status='failed'
                WHERE id=?
            ");

            $update->execute([$email['id']]);

        }
    }

    echo "Worker completed";

} catch (Exception $e) {

    echo $e->getMessage();
}