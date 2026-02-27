<?php

ignore_user_abort(true);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,
"http://localhost/virtual_internship_hub/backend/email/email_worker.php");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

curl_exec($ch);

curl_close($ch);

echo json_encode(["success"=>true]);