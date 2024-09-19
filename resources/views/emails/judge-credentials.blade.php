<!DOCTYPE html>
<html>
<head>
    <title>Judge Credentials</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;

            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

        }
        h1 {
            color: #333333;
            text-align: center;
        }
        p {
            color: #666666;
            line-height: 1.6;
        }
        .credentials {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .credentials p {
            margin: 0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, {{$name}}!</h1>
        <p>Hello,</p>
        <p>We are excited to have you as a judge for our contest. Here are your dashboard credentials:</p>
        <div class="credentials">
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
        </div>
        <p>Please keep this information secure and do not share it with anyone.</p>
        <a href="http://127.0.0.1:5173/judge-login" class="button">Go to Judge Dashboard</a>
        <div class="footer">
            <p>Thank you for being a part of our contest.</p>
        </div>
    </div>
</body>
</html>
