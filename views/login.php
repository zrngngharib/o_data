<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەژوورەوە</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- فۆنتی Zain -->
    <link href="https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,200;0,300;0,400;0,700;0,800;0,900;1,300;1,400&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Zain', sans-serif;
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 px-4">

    <div class="w-full max-w-md p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-900">چوونەژوورەوە</h2>

        <form action="login_process.php" method="POST" class="space-y-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">ناوی بەکارهێنەر</label>
                <input type="text" name="username" required
                    class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300 text-right">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">وشەی تێپەڕ</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300 text-right">
            </div>

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember_me" class="form-checkbox">
                    <span class="ml-2">بۆ ماوەی ٣٠ ڕۆژ لە ژورەوەبم</span>
                </label>
            </div>

            <button type="submit"
                class="w-full px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                چوونەژوورەوە
            </button>
        </form>
    </div>

</body>
</html>
