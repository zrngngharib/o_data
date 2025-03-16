<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Zain Font -->
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../fonts') format('truetype');
        }
        body {
            font-family: 'Zain', sans-serif;
            background: linear-gradient(135deg, #dee8ff 0%, #f5f7fa 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="glass text-center max-w-md w-full">
    <h1 class="text-4xl font-bold mb-6 animate-pulse text-red-500"><i class="fas fa-exclamation-triangle"></i> 404</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mt-4">لاپەڕە نەدۆزرایەوە</h2>
        <p class="text-gray-600 mt-2">ببورە، ئەو لاپەڕەی کە بۆی دەگەڕێیت  نەدۆزرایەوە.</p>
        <a href="../views/dashboard.php" class="mt-6 inline-block bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-home"></i> گەڕانەوە بۆ سەرەتا
        </a>
    </div>
</body>
</html>