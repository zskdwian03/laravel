<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin MBJEK</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f2f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: white;
            padding: 2rem 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        form h2 {
            text-align: center;
            color: #192559;
            margin-bottom: 1.5rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 2px solid #ccc;
            border-radius: 12px;
            font-size: 1rem;
            transition: 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #192559;
            outline: none;
            box-shadow: 0 0 5px #19255988;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #192559;
            color: white;
            font-size: 1.1rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: #0f1a3b;
        }

        button:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <h2>👩‍💻 Login Admin MBJEK</h2>
        <input type="email" name="email" placeholder="Email Admin" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login Admin 🚀</button>
    </form>
</body>
</html>
