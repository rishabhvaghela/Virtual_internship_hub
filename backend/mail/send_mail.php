<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



function sendOTP($toEmail, $otp)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rishabh.vaghela13@gmail.com';
        $mail->Password   = 'yzcwpbnuvwnzogyq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('rishabh.vaghela13@gmail.com', 'Virtual Internship Hub');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Verification Code';

        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Email Verification</title>
</head>
<body style="margin:0; padding:0; background:#0b0f1a; font-family:Segoe UI, Roboto, Arial, sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0b0f1a; padding:40px 0;">
    <tr>
      <td align="center">

        <table width="520" cellpadding="0" cellspacing="0" style="
          background:linear-gradient(145deg,#0f172a,#020617);
          border-radius:18px;
          box-shadow:0 0 40px rgba(79,70,229,0.35);
          overflow:hidden;
        ">

          <!-- HEADER -->
          <tr>
            <td style="padding:28px; text-align:center; border-bottom:1px solid #1e293b;">
              <h1 style="
                margin:0;
                font-size:22px;
                color:#e0e7ff;
                letter-spacing:1px;
              ">
                VIRTUAL INTERNSHIP HUB
              </h1>
              <p style="margin:6px 0 0; color:#94a3b8; font-size:13px;">
                Secure Email Verification
              </p>
            </td>
          </tr>

          <!-- CONTENT -->
          <tr>
            <td style="padding:32px; color:#c7d2fe;">
              <h2 style="margin-top:0; font-size:20px; color:#ffffff;">
                Verify Your Email Address
              </h2>

              <p style="font-size:14px; line-height:1.7; color:#cbd5f5;">
                Use the verification code below to complete your registration.
                This code is valid for <strong>10 minutes</strong>.
              </p>

              <!-- OTP BOX -->
              <div style="
                margin:30px auto;
                text-align:center;
                font-size:34px;
                letter-spacing:8px;
                font-weight:600;
                color:#ffffff;
                padding:18px;
                background:linear-gradient(135deg,#4f46e5,#22d3ee);
                border-radius:14px;
                width:fit-content;
              ">
                ' . $otp . '
              </div>

              <p style="font-size:13px; color:#94a3b8; text-align:center;">
                Do not share this code with anyone.
              </p>
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td style="
              padding:18px;
              text-align:center;
              font-size:12px;
              color:#64748b;
              border-top:1px solid #1e293b;
            ">
              © ' . date("Y") . ' Virtual Internship Hub<br>
              Secure • Reliable • Professional
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>
</html>
';



        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}


function sendResetLink($toEmail, $resetLink)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rishabh.vaghela13@gmail.com';
        $mail->Password   = 'yzcwpbnuvwnzogyq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('rishabh.vaghela13@gmail.com', 'Virtual Internship Hub');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';

        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Password Reset</title>
</head>
<body style="margin:0; padding:0; background:#0b0f1a; font-family:Segoe UI, Roboto, Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
<tr>
<td align="center">

<table width="520" cellpadding="0" cellspacing="0" style="
  background:linear-gradient(160deg,#020617,#0f172a);
  border-radius:18px;
  box-shadow:0 0 45px rgba(99,102,241,0.35);
">

<tr>
<td style="padding:30px; text-align:center;">
  <h1 style="color:#e0e7ff; margin-bottom:8px;">
    Virtual Internship Hub
  </h1>
  <p style="color:#94a3b8; font-size:14px;">
    Password Reset Request
  </p>
</td>
</tr>

<tr>
<td style="padding:30px; color:#cbd5f5;">
  <p style="font-size:15px; line-height:1.7;">
    We received a request to reset your password. Click the button below to continue.
    This link will expire in <strong>15 minutes</strong>.
  </p>

  <div style="text-align:center; margin:30px 0;">
    <a href="' . $resetLink . '" style="
      background:linear-gradient(135deg,#6366f1,#22d3ee);
      color:#ffffff;
      padding:14px 28px;
      text-decoration:none;
      border-radius:10px;
      font-size:15px;
      font-weight:600;
      display:inline-block;
      box-shadow:0 10px 25px rgba(34,211,238,0.4);
    ">
      Reset Password
    </a>
  </div>

  <p style="font-size:13px; color:#94a3b8;">
    If you didn’t request this, you can safely ignore this email.
  </p>
</td>
</tr>

<tr>
<td style="padding:18px; text-align:center; font-size:12px; color:#64748b;">
  © ' . date("Y") . ' Virtual Internship Hub
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
';


        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
