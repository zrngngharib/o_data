<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەخێربێیت بۆ O_Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('fonts/Zain.ttf') format('truetype');
        }
        body {
            font-family: 'Zain', sans-serif;
            background-color: #dee8ff;
        }
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            border-radius: 2rem;
            padding: 2rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="glass text-center max-w-md w-full">
        <h1 class="text-4xl font-bold mb-6 animate-pulse">📊 O_Data</h1>
        <p class="mb-4 text-lg">بەخێربێیت بۆ سیستەمی بەڕێوەبردنی داتاکان</p>
        <a href="views/login.php" class="btn btn-primary text-white px-4 py-2 rounded-pill shadow-md transition-transform hover:scale-105" style="background-color: #4F46E5;">
            چوونەژوورەوە
        </a>
    </div>
</body>
</html>
