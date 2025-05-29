<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 Unauthorized</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #3b65ea;
            color: #1a202c;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .error-code {
            font-size: 72px;
            font-weight: 600;
            color: #ef4444;
        }
        .error-message {
            font-size: 24px;
            margin: 10px 0 20px;
        }
        a.button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }
        a.button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-message">Unauthorized Access</div>
        <p>You don't have permission to access this page.</p>
        <a href="{{ url('/') }}" class="button" style="background-color:#3b65ea !important;">Go to Homepage</a>
    </div>
</body>
</html>
