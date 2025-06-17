<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.1);
            max-width: 500px;
            width: 100%;
        }
        .error-code {
            font-size: 96px;
            font-weight: 800;
            color: #0d6efd;
        }
        .error-message {
            font-size: 20px;
            color: #6c757d;
        }
        @media (max-width: 600px) {
            .error-code {
                font-size: 72px;
            }
            .error-message {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div class="error-container">
    <div class="error-code">404</div>
    <p class="error-message">Sorry, the page you are looking for could not be found.</p>
    <button class="btn btn-primary mt-4" onclick="history.back()">← Go Back</button>
</div>

</body>
</html>
