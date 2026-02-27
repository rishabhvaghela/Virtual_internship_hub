<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/send_mail.php';

$stmt = $pdo->prepare("
    SELECT * FROM email_queue
    WHERE status='pending'
    ORDER BY id ASC
    LIMIT 5
");

$stmt->execute();

$emails = $stmt->fetchAll();

foreach ($emails as $email)
{
    $sent = sendRawEmail(
        $email['to_email'],
        $email['subject'],
        $email['body']
    );

    if ($sent)
    {
        $update = $pdo->prepare("
            UPDATE email_queue
            SET status='sent',
                sent_at=NOW()
            WHERE id=?
        ");

        $update->execute([$email['id']]);
    }
    else
    {
        $update = $pdo->prepare("
            UPDATE email_queue
            SET status='failed'
            WHERE id=?
        ");

        $update->execute([$email['id']]);
    }
}

echo "Queue processed";