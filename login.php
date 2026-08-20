<?php

// Ten plik obsługuje formularz logowania użytkownika.

// Sesja pozwala zapamiętać, kto jest zalogowany.
session_start();

// Dołączamy połączenie z bazą danych.
require_once __DIR__ . "/config/database.php";

// Lista błędów, które później pokażemy nad formularzem.
$errors = [];
$email = "";

// Zalogowany użytkownik nie musi ponownie otwierać logowania
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

// Ten blok wykonuje się dopiero po kliknięciu „Zaloguj się”.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pobieramy e-mail i hasło przesłane z formularza.
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Najpierw sprawdzamy, czy dane mają poprawny podstawowy format.
    if ($email === "") {
        $errors[] = "Podaj adres e-mail.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Podaj poprawny adres e-mail.";
    }

    if ($password === "") {
        $errors[] = "Podaj hasło.";
    }

    // Do bazy pytamy dopiero wtedy, gdy formularz nie ma błędów.
    if (empty($errors)) {
        // Szukamy użytkownika o podanym adresie e-mail.
        $statement = $pdo->prepare(
            "SELECT id, name, email, password_hash
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        $statement->execute([$email]);

        // fetch() pobiera znaleziony rekord albo zwraca false.
        $user = $statement->fetch();

        // Sprawdzamy jednocześnie, czy użytkownik istnieje i czy hasło się zgadza.
        if (
            !$user ||
            !password_verify($password, $user["password_hash"])
        ) {
            $errors[] = "Nieprawidłowy adres e-mail lub hasło.";
        } else {
            // Po poprawnym logowaniu zmieniamy ID sesji dla większego bezpieczeństwa.
            session_regenerate_id(true);

            // Zapisujemy podstawowe dane użytkownika w sesji.
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];

            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Logowanie — StudyFlow</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <main>
        <h1>Logowanie</h1>

        <!-- Jeżeli logowanie się nie udało, pokazujemy listę błędów. -->
        <?php if (!empty($errors)): ?>
            <div>
                <h2>Nie udało się zalogować:</h2>

                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?= htmlspecialchars(
                                $error,
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formularz wysyła dane z powrotem do tego samego pliku metodą POST. -->
        <form action="login.php" method="post">
            <div>
                <label for="email">Adres e-mail:</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="150"
                    value="<?= htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                    required
                >
            </div>

            <div>
                <label for="password">Hasło:</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <button type="submit">Zaloguj się</button>
        </form>

        <p>
            Nie masz konta?
            <a href="register.php">Zarejestruj się</a>
        </p>

        <p>
            <a href="index.php">Wróć na stronę główną</a>
        </p>
    </main>
</body>
</html>