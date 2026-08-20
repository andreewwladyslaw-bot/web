<?php
// Strona startowa StudyFlow. Uruchamiamy sesję, aby aplikacja mogła korzystać z logowania.
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudyFlow</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Główna zawartość strony startowej. -->
    <main>
        <h1>StudyFlow</h1>

        <p>Twój szkolny planer zadań.</p>

        <!-- Linki prowadzą do logowania i rejestracji. -->
        <nav>
            <a href="login.php">Zaloguj się</a>
            <a href="register.php">Zarejestruj się</a>
        </nav>
    </main>

    <script src="assets/js/app.js"></script>
</body>
</html>