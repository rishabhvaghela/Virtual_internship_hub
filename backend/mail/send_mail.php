<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/* =========================================================
!  CORE SMTP SENDER
========================================================= */

function sendRawEmail($toEmail, $subject, $body)
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
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        return true;
    } catch (Exception $e) {
        return false;
    }
}


/* =========================================================
!  EMAIL QUEUE FUNCTION (NON BLOCKING)
========================================================= */
function queueEmail($toEmail, $subject, $body)
{
    global $pdo;

    try {

        $stmt = $pdo->prepare("
            INSERT INTO email_queue (to_email, subject, body, status, created_at)
            VALUES (?, ?, ?, 'pending', NOW())
        ");

        $stmt->execute([$toEmail, $subject, $body]);


        /* =====================================
           START WORKER IN BACKGROUND (REAL FIX)
        ===================================== */

        $workerPath = __DIR__ . '/../email/email_worker.php';

        // Windows background execution
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

            pclose(popen("start /B php \"$workerPath\"", "r"));

        } else {

            // Linux background execution
            exec("php \"$workerPath\" > /dev/null 2>&1 &");

        }

        return true;

    } catch (Exception $e) {

        return false;

    }
}

/* =========================================================
!  OTP EMAIL (INSTANT)
========================================================= */
function sendOTP($toEmail, $otp)
{
    $subject = "Your Verification Code - Virtual Internship Hub";

    $body = '
<body style="margin:0;background:#0b0f19;font-family:Segoe UI,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px;">
<tr>
<td align="center">

<table width="520" style="
background:#111827;
border-radius:16px;
overflow:hidden;
box-shadow:0 0 40px rgba(59,130,246,0.4);
">

<!-- HEADER -->
<tr>
<td style="
background:linear-gradient(135deg,#3b82f6,#06b6d4);
padding:25px;
text-align:center;
color:white;
">

<div style="font-size:22px;font-weight:bold;">
Verification Code 🔐
</div>

<div style="font-size:13px;opacity:0.9;margin-top:5px;">
Virtual Internship Hub Security
</div>

</td>
</tr>

<!-- BODY -->
<tr>
<td style="padding:30px;color:#e5e7eb;">

Hello,

<br><br>

Use the following verification code:

<br><br>

<div style="
background:#0f172a;
padding:20px;
border-radius:12px;
text-align:center;
border:1px solid #1f2937;
">

<span style="
font-size:32px;
letter-spacing:8px;
font-weight:bold;
color:#3b82f6;
">
' . $otp . '
</span>

</div>

<br>

This code will expire soon for security reasons.

<br><br>

Do not share this code with anyone.

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="
padding:18px;
text-align:center;
color:#6b7280;
font-size:12px;
border-top:1px solid #1f2937;
">

© ' . date("Y") . ' Virtual Internship Hub  
<br>
Secure • Reliable • Professional

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>';

    return sendRawEmail($toEmail, $subject, $body);
}


/* =========================================================
!  RESET EMAIL (INSTANT)
========================================================= */
function sendResetLink($toEmail, $resetLink)
{
    $subject = "Reset Your Password - Virtual Internship Hub";

    $body = '
<body style="margin:0;background:#0b0f19;font-family:Segoe UI,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px;">
<tr>
<td align="center">

<table width="520" style="
background:#111827;
border-radius:16px;
overflow:hidden;
box-shadow:0 0 40px rgba(239,68,68,0.4);
">

<!-- HEADER -->
<tr>
<td style="
background:linear-gradient(135deg,#ef4444,#f97316);
padding:25px;
text-align:center;
color:white;
font-size:22px;
font-weight:bold;
">

Password Reset Request

</td>
</tr>

<!-- BODY -->
<tr>
<td style="padding:30px;color:#e5e7eb;">

We received a request to reset your password.

<br><br>

Click the button below:

<br><br>

<div style="text-align:center;">

<a href="' . $resetLink . '" style="
background:#ef4444;
color:white;
padding:12px 28px;
text-decoration:none;
border-radius:8px;
font-weight:bold;
display:inline-block;
">
Reset Password
</a>

</div>

<br><br>

Or copy and paste this link:

<br>

<span style="color:#60a5fa;">
' . $resetLink . '
</span>

<br><br>

If you did not request this, please ignore this email.

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="
padding:18px;
text-align:center;
color:#6b7280;
font-size:12px;
border-top:1px solid #1f2937;
">

© ' . date("Y") . ' Virtual Internship Hub  
<br>
Security Notification

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>';

    return sendRawEmail($toEmail, $subject, $body);
}


/* =========================================================
!  APPLICATION CONFIRMATION (QUEUE)
========================================================= */
function sendApplicationConfirmation($toEmail, $studentName, $internshipTitle, $companyName)
{
    $subject = "Application Submitted Successfully";

    $body = '
<body style="margin:0;background:#0b0f19;font-family:Segoe UI,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
<tr>
<td align="center">

<table width="520" style="
background:#111827;
border-radius:16px;
overflow:hidden;
box-shadow:0 0 40px rgba(79,70,229,0.4);
">

<tr>
<td style="
background:linear-gradient(135deg,#4f46e5,#06b6d4);
padding:25px;
text-align:center;
color:white;
font-size:22px;
font-weight:bold;
">
Application Submitted Successfully
</td>
</tr>

<tr>
<td style="padding:30px;color:#e5e7eb;">

Hello <b>' . $studentName . '</b>,
<br><br>

You successfully applied for:

<br><br>

<div style="
background:#1f2937;
padding:18px;
border-radius:10px;
">

<b style="font-size:18px;color:#60a5fa;">
' . $internshipTitle . '
</b>

<br>

<span style="color:#9ca3af;">
Company: ' . $companyName . '
</span>

</div>

<br>

We will notify you when the company reviews your application.

<br><br>

<span style="color:#10b981;font-weight:bold;">
Status: Under Review
</span>

</td>
</tr>

<tr>
<td style="padding:15px;text-align:center;color:#6b7280;">
© ' . date("Y") . ' Virtual Internship Hub
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>';

    $result = queueEmail($toEmail, $subject, $body);

    // trigger background worker
    @file_get_contents(
        "http://localhost/virtual_internship_hub/backend/email/trigger_email_worker.php"
    );

    return $result;
}


/* =========================================================
!  STATUS EMAIL (QUEUE)
========================================================= */
function sendApplicationStatusEmail($toEmail, $studentName, $internshipTitle, $companyName, $status)
{
    global $pdo;

    try {

        // ============================
        // STATUS CONFIG
        // ============================

        if ($status === 'accepted') {
            $subject = "Application Accepted - Virtual Internship Hub";

            $headerColor = "#10b981";

            $statusTitle = "Application Accepted";

            $message = "
                Congratulations! Your application has been accepted.
                The company may contact you soon for next steps.
            ";

            $statusBadge = "
                <span style='color:#10b981;font-weight:bold;'>
                Status: Accepted
                </span>
            ";
        } else {
            $subject = "Application Update";

            $headerColor = "#ef4444";

            $statusTitle = "Application Update";

            $message = "
                Thank you for your interest.
                Unfortunately, the company selected another candidate.
                We encourage you to apply for other internships.
            ";

            $statusBadge = "
                <span style='color:#ef4444;font-weight:bold;'>
                Status: Not Selected
                </span>
            ";
        }
        $body = '
        <body style="margin:0;background:#0b0f19;font-family:Segoe UI,Arial,sans-serif;">

        <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px;">
        <tr>
        <td align="center">

        <table width="520" style="
        background:#111827;
        border-radius:16px;
        overflow:hidden;
        box-shadow:0 0 40px rgba(79,70,229,0.35);
        ">

        <!-- HEADER -->
        <tr>
        <td style="
        background:' . $headerColor . ';
        padding:25px;
        text-align:center;
        color:white;
        font-size:22px;
        font-weight:bold;
        ">
        ' . $statusTitle . '
        </td>
        </tr>

        <!-- BODY -->
        <tr>
        <td style="padding:30px;color:#e5e7eb;">

        Hello <b>' . $studentName . '</b>,
        <br><br>

        ' . $message . '

        <br><br>

        <div style="
        background:#1f2937;
        padding:18px;
        border-radius:10px;
        ">

        <b style="font-size:18px;color:#60a5fa;">
        ' . $internshipTitle . '
        </b>

        <br>

        <span style="color:#9ca3af;">
        Company: ' . $companyName . '
        </span>

        </div>

        <br>

        ' . $statusBadge . '

        <br><br>

        Thank you for using Virtual Internship Hub.

        </td>
        </tr>

        <!-- FOOTER -->
        <tr>
        <td style="
        padding:15px;
        text-align:center;
        color:#6b7280;
        font-size:12px;
        ">
        © ' . date("Y") . ' Virtual Internship Hub
        </td>
        </tr>

        </table>

        </td>
        </tr>
        </table>

        </body>
        ';


        // ============================
        //  QUEUE EMAIL (BACKGROUND)
        // ============================

        $result = queueEmail($toEmail, $subject, $body);

        // trigger background worker
        @file_get_contents(
            "http://localhost/virtual_internship_hub/backend/email/trigger_email_worker.php"
        );

        return $result;
    } catch (Exception $e) {
        return false;
    }
}

/* =========================================================
   ! INTERVIEW EMAIL (QUEUE)
========================================================= */
function sendInterviewEmail($toEmail, $studentName, $internshipTitle, $companyName, $date, $note)
{
    $formattedDate = date("d M Y h:i A", strtotime($date));

    $subject = "Interview Scheduled";

    $body = '
<body style="margin:0;padding:0;background:#0b0f19;font-family:Segoe UI,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
<tr>
<td align="center">

<table width="520" cellpadding="0" cellspacing="0" style="
background:#111827;
border-radius:18px;
overflow:hidden;
box-shadow:0 0 45px rgba(59,130,246,0.35);
">

<!-- HEADER -->
<tr>
<td style="
background:linear-gradient(135deg,#3b82f6,#06b6d4);
padding:26px;
text-align:center;
color:white;
">

<div style="font-size:22px;font-weight:bold;">
Interview Scheduled 🎯
</div>

<div style="font-size:13px;opacity:0.9;margin-top:5px;">
Virtual Internship Hub
</div>

</td>
</tr>


<!-- BODY -->
<tr>
<td style="padding:30px;color:#e5e7eb;">

Hello <b>' . $studentName . '</b>,

<br><br>

Great news! Your interview has been successfully scheduled.

<br><br>


<!-- INTERVIEW DETAILS BOX -->
<div style="
background:#1f2937;
padding:20px;
border-radius:12px;
border-left:4px solid #3b82f6;
">

<div style="margin-bottom:10px;">
<span style="color:#9ca3af;">Internship:</span><br>
<b style="color:#60a5fa;font-size:16px;">
' . $internshipTitle . '
</b>
</div>

<div style="margin-bottom:10px;">
<span style="color:#9ca3af;">Company:</span><br>
<b>' . $companyName . '</b>
</div>

<div style="margin-bottom:10px;">
<span style="color:#9ca3af;">Date & Time:</span><br>
<b>' . $formattedDate . '</b>
</div>

<div>
<span style="color:#9ca3af;">Note:</span><br>
' . $note . '
</div>

</div>


<br>


<!-- STATUS -->
<div style="
background:#0f172a;
padding:12px;
border-radius:8px;
text-align:center;
">

<span style="
color:#3b82f6;
font-weight:bold;
font-size:14px;
">
Status: Interview Scheduled
</span>

</div>


<br>

Please make sure to be available on time and prepare accordingly.

<br><br>

Best of luck! 🚀

</td>
</tr>


<!-- FOOTER -->
<tr>
<td style="
padding:18px;
text-align:center;
color:#6b7280;
font-size:12px;
border-top:1px solid #1f2937;
">

© ' . date("Y") . ' Virtual Internship Hub  
<br>
Secure • Professional • Reliable

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>';

    //* Existing queue system (UNCHANGED)
    $result = queueEmail($toEmail, $subject, $body);

    //* NEW: trigger background worker (NON-BLOCKING)
    @file_get_contents(
        "http://localhost/virtual_internship_hub/backend/email/trigger_email_worker.php"
    );

    return $result;
}

/* =========================================================
   ! COMPANY NOTIFICATION EMAIL (QUEUE + FUTURISTIC)
========================================================= */
function sendCompanyNewApplicantEmail(
    $companyEmail,
    $companyName,
    $studentName,
    $studentEmail,
    $internshipTitle
) {

    $subject = "New Applicant Received • Virtual Internship Hub";

    $body = '

<body style="
margin:0;
background:#020617;
font-family:Segoe UI,Arial,sans-serif;
">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">

<tr>
<td align="center">

<table width="540" style="
background:linear-gradient(180deg,#020617,#0f172a);
border-radius:20px;
overflow:hidden;
box-shadow:0 0 60px rgba(59,130,246,0.35);
border:1px solid rgba(59,130,246,0.2);
">

<!-- HEADER -->
<tr>
<td style="
background:linear-gradient(135deg,#3b82f6,#06b6d4);
padding:28px;
text-align:center;
color:white;
">

<div style="font-size:24px;font-weight:bold;">
New Applicant Received ⚡
</div>

<div style="opacity:0.9;font-size:13px;margin-top:5px;">
Virtual Internship Hub • Hiring System
</div>

</td>
</tr>


<!-- BODY -->
<tr>
<td style="padding:35px;color:#e2e8f0;">

Hello <b>' . $companyName . '</b>,

<br><br>

A new student has applied to your internship.

<br><br>


<div style="
background:#020617;
border:1px solid rgba(59,130,246,0.25);
padding:20px;
border-radius:12px;
">

<div style="margin-bottom:12px;">
<span style="color:#94a3b8;">Internship:</span><br>
<b style="font-size:17px;color:#38bdf8;">
' . $internshipTitle . '
</b>
</div>


<div style="margin-bottom:12px;">
<span style="color:#94a3b8;">Applicant Name:</span><br>
<b>' . $studentName . '</b>
</div>


<div>
<span style="color:#94a3b8;">Applicant Email:</span><br>
<b>' . $studentEmail . '</b>
</div>

</div>


<br>


<div style="
background:#020617;
padding:14px;
border-radius:10px;
text-align:center;
border:1px solid rgba(16,185,129,0.3);
">

<span style="
color:#10b981;
font-weight:bold;
">
Action Required: Review Application
</span>

</div>


<br>

Login to your dashboard to review applicant and schedule interview.

</td>
</tr>


<!-- FOOTER -->
<tr>
<td style="
padding:20px;
text-align:center;
color:#64748b;
font-size:12px;
border-top:1px solid rgba(59,130,246,0.2);
">

© ' . date("Y") . ' Virtual Internship Hub  
<br>
Smart Hiring • Fast • Secure

</td>
</tr>

</table>

</td>
</tr>

</table>

</body>

';

    //* Existing queue system (UNCHANGED)
    $result = queueEmail($companyEmail, $subject, $body);

    //* NEW: trigger background worker (NON-BLOCKING)
    @file_get_contents(
        "http://localhost/virtual_internship_hub/backend/email/trigger_email_worker.php"
    );

    return $result;
}
