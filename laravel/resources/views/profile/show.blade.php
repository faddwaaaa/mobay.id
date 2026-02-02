<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>{{ $user->name }}</h1>
    <p>@ {{ $user->username }}</p>

    <p style="margin-top:20px; color:#666;">
        Preview page aktif
    </p>

</body>
</html>
