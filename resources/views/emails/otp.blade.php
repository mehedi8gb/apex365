<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background-color: #4f46e5;
            padding: 2rem;
            text-align: center;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .content {
            padding: 2rem;
        }
        .title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .description {
            font-size: 1rem;
            line-height: 1.5rem;
            color: #4b5563;
            text-align: center;
            margin-bottom: 2rem;
        }
        .otp-container {
            background-color: #e0e7ff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .otp-code {
            font-size: 2.25rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #4f46e5;
            text-align: center;
        }
        .copy-instruction {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
            margin-top: 1rem;
        }
        .button {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            padding: 0.75rem 1.5rem;
            background-color: #4f46e5;
            color: #ffffff;
            text-decoration: none;
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }
        .button:hover {
            background-color: #4338ca;
        }
        .footer {
            background-color: #f9fafb;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }
        /* Responsive adjustments */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                border-radius: 0;
            }
            .content {
                padding: 1.5rem;
            }
            .otp-code {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="https://via.placeholder.com/150x50" alt="Logo" class="logo">
    </div>
    <div class="content">
        <h1 class="title">Your One-Time Password (OTP)</h1>
        <p class="description">Please use the following OTP to complete your verification:</p>
        <div class="otp-container">
            <div class="otp-code">{{ $otp }}</div>
        </div>
    </div>
    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
        <p style="margin-top: 0.5rem;">© 2024 Your Company Name. All rights reserved.</p>
    </div>
</div>
</body>
</html>
