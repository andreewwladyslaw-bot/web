<?php

// Ten plik tworzy nowe konto użytkownika.

// Potrzebujemy połączenia z bazą, aby sprawdzić e-mail i zapisać konto.
require_once __DIR__ . "/config/database.php";

// W tej tablicy zbieramy błędy formularza.
$errors = [];
$name = "";
$email = "";

// Parametr success=1 pozwala pokazać komunikat po udanej rejestracji.
$success = isset($_GET["success"]);

// Kod w tym bloku działa po wysłaniu formularza rejestracji.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pobieramy wartości wpisane przez użytkownika.
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $passwordConfirm = $_POST["password_confirm"] ?? "";

    // Sprawdzenie pustych pól
    if ($name === "") {
        $errors[] = "Podaj imię.";
    }

    if ($email === "") {
        $errors[] = "Podaj adres e-mail.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Podaj poprawny adres e-mail.";
    }

    if ($password === "") {
        $errors[] = "Podaj hasło.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Hasło musi mieć co najmniej 8 znaków.";
    }

    if ($passwordConfirm === "") {
        $errors[] = "Powtórz hasło.";
    } elseif ($password !== $passwordConfirm) {
        $errors[] = "Podane hasła nie są takie same.";
    }

    // Sprawdzenie długości danych zgodnie ze strukturą bazy
    if (strlen($name) > 100) {
        $errors[] = "Imię jest zbyt długie.";
    }

    if (strlen($email) > 150) {
        $errors[] = "Adres e-mail jest zbyt długi.";
    }

    // Jeżeli podstawowa walidacja przeszła poprawnie
    if (empty($errors)) {
        // Sprawdzamy, czy taki e-mail nie jest już zajęty.
        $checkUser = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $checkUser->execute([$email]);

        if ($checkUser->fetch()) {
            $errors[] = "Konto z tym adresem e-mail już istnieje.";
        } else {
            // Hasła nie zapisujemy w bazie jako zwykły tekst. Tworzymy bezpieczny hash.
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            try {
                // Dodajemy nowego użytkownika do tabeli users.
                $insertUser = $pdo->prepare(
                    "INSERT INTO users (name, email, password_hash)
                     VALUES (?, ?, ?)"
                );

                $insertUser->execute([
                    $name,
                    $email,
                    $passwordHash
                ]);

                // Po zapisie odświeżamy stronę z informacją o sukcesie.
                header("Location: register.php?success=1");
                exit;
            } catch (PDOException $e) {
                $errors[] = "Nie udało się utworzyć konta. Spróbuj ponownie.";
            }
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

    <title>Rejestracja — StudyFlow</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <main>
        <h1>Utwórz konto</h1>

        <!-- Komunikat po poprawnym utworzeniu konta. -->
        <?php if ($success): ?>
            <p>
                Konto zostało utworzone poprawnie.
                Możesz się teraz zalogować.
            </p>

            <p>
                <a href="login.php">Przejdź do logowania</a>
            </p>
        <?php endif; ?>

        <!-- Jeżeli są błędy, wyświetlamy je użytkownikowi. -->
        <?php if (!empty($errors)): ?>
            <div>
                <h2>Popraw następujące błędy:</h2>

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

        <!-- Formularz rejestracji wysyła dane metodą POST. -->
        <form action="register.php" method="post">
            <div>
                <label for="name">Imię:</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $name,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                    required
                >
            </div>

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
                    minlength="8"
                    required
                >
            </div>

            <div>
                <label for="password_confirm">
                    Powtórz hasło:
                </label>

                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    minlength="8"
                    required
                >
            </div>

            <button type="submit">Zarejestruj się</button>
        </form>

        <p>
            Masz już konto?
            <a href="login.php">Zaloguj się</a>
        </p>

        <p>
            <a href="index.php">Wróć na stronę główną</a>
        </p>
    </main>
</body>
</html>