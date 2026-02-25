<?php
session_start();

if (!isset($_SESSION['user_id'])) {

    // Prevent browser caching (important for back button issue)
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Session Expired</title>
         <link rel="icon" type="image/png" href="assets/images/logo/Web Icon.png">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea, #764ba2);
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
            }

            .modal {
                background: #fff;
                padding: 40px;
                border-radius: 15px;
                text-align: center;
                width: 350px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.2);
                animation: fadeIn 0.4s ease-in-out;
            }

            h2 {
                margin-bottom: 15px;
                color: #333;
            }

            p {
                color: #666;
                margin-bottom: 25px;
            }

            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #667eea;
                color: #fff;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
            }

            .btn:hover {
                background: #5a67d8;
            }

            @keyframes fadeIn {
                from { transform: scale(0.9); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
        </style>
    </head>
    <body>
        <div class="modal">
            <h2>Session Expired</h2>
            <p>Your session is no longer active.<br>Please login again to continue.</p>
            <a href="/virtual_internship_hub/login.html" class="btn">Go to Login</a>
        </div>
    </body>
    </html>
    ';
    exit;
}
?>