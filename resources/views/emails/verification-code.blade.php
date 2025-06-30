<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi MBJEK</title>
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
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/mbjek_logo.png') }}" alt="MBJEK Logo">
            <h2 style="margin: 10px 0 0 0;">MBJEK</h2>
        </div>
        
        <h3>Halo {{ $user->username }},</h3>
        
        <p>Terima kasih telah mendaftar di MBJEK. Berikut adalah kode verifikasi untuk akun Anda:</p>
        
        <div class="code">{{ $verificationCode }}</div>
        
        <p>Silakan masukkan kode ini pada halaman verifikasi untuk menyelesaikan proses pendaftaran.</p>
        
        <p>Jika Anda tidak merasa mendaftar di MBJEK, silakan abaikan email ini.</p>
        
        <p>Terima kasih,<br>Tim MBJEK</p>
    </div>
</body>
</html>