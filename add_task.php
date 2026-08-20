<?php

// Ten plik odbiera formularz dodawania zadania i zapisuje zadanie w bazie.

// Sprawdzamy logowanie i pobieramy połączenie z bazą.
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";

// Ten plik powinien być uruchamiany tylko po wysłaniu formularza metodą POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// Pobieramy ID aktualnie zalogowanego użytkownika.
$userId = $_SESSION["user_id"];

// Pobieramy dane przesłane z formularza. trim() usuwa zbędne spacje.
$title = trim($_POST["title"] ?? "");
$category = $_POST["category"] ?? "";
$priority = $_POST["priority"] ?? "";
$isImportant = isset($_POST["is_important"]) ? 1 : 0;
$dueDate = trim($_POST["due_date"] ?? "");

// Tutaj będziemy zbierać komunikaty o błędach formularza.
$errors = [];

// Lista kategorii, które naprawdę akceptuje serwer.
$allowedCategories = [
    "programowanie",
    "matematyka",
    "jezyk_polski",
    "jezyk_angielski",
    "inne"
];

// Lista dozwolonych priorytetów.
$allowedPriorities = [
    "niski",
    "sredni",
    "wysoki"
];

// Sprawdzamy poprawność nazwy zadania.
if ($title === "") {
    $errors[] = "Podaj nazwę zadania.";
} elseif (strlen($title) < 3) {
    $errors[] = "Nazwa zadania musi mieć co najmniej 3 znaki.";
} elseif (strlen($title) > 150) {
    $errors[] = "Nazwa zadania może mieć maksymalnie 150 znaków.";
}

// Sprawdzamy, czy użytkownik wybrał kategorię z naszej listy.
if (!in_array($category, $allowedCategories, true)) {
    $errors[] = "Wybierz poprawną kategorię.";
}

// Tak samo sprawdzamy priorytet.
if (!in_array($priority, $allowedPriorities, true)) {
    $errors[] = "Wybierz poprawny priorytet.";
}

// Jeżeli podano termin, sprawdzamy format daty.
if ($dueDate !== "") {
    $date = DateTime::createFromFormat("Y-m-d", $dueDate);

    if (!$date || $date->format("Y-m-d") !== $dueDate) {
        $errors[] = "Podaj poprawny termin wykonania.";
    }
} else {
    $dueDate = null;
}

// Jeśli znaleźliśmy błąd, zapisujemy komunikat w sesji i wracamy do panelu.
if (!empty($errors)) {
    $_SESSION["task_error"] = implode(" ", $errors);

    header("Location: dashboard.php");
    exit;
}

// Gdy dane są poprawne, zapisujemy nowe zadanie do tabeli tasks.
try {
    // prepare() tworzy zapytanie z miejscami na dane użytkownika.
    $statement = $pdo->prepare(
        "INSERT INTO tasks (
            user_id,
            title,
            category,
            priority,
            is_important,
            due_date
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    // execute() wstawia konkretne wartości w znaki zapytania.
    $statement->execute([
        $userId,
        $title,
        $category,
        $priority,
        $isImportant,
        $dueDate
    ]);

    $_SESSION["task_success"] = "Zadanie zostało dodane poprawnie.";
} catch (PDOException $e) {
    // Gdy zapis do bazy się nie uda, pokazujemy prosty komunikat użytkownikowi.
    $_SESSION["task_error"] =
        "Nie udało się zapisać zadania. Spróbuj ponownie.";
}

// Po zakończeniu zawsze wracamy do dashboardu.
header("Location: dashboard.php");
exit;