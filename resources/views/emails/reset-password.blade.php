<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #192b5d;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 5px 5px 0 0;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            letter-spacing: 5px;
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h3>Halo {{ $name }},</h3>
    <p>Kami menerima permintaan untuk mereset password akunmu.</p>
    <p>Klik link di bawah ini untuk mengatur ulang password:</p>
    <a href="{{ $url }}">{{ $url }}</a>
    <p>Kalau kamu tidak meminta reset password, abaikan email ini.</p>
    <br>
    <p>Salam,<br>Tim MBJEK</p>
</body>
</html>
