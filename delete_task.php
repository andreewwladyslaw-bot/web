<?php

// Ten plik usuwa wybrane zadanie zalogowanego użytkownika.

// Najpierw sprawdzamy logowanie i łączymy się z bazą.
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";

// Usuwanie akceptujemy tylko z formularza POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// Pobieramy task_id i od razu sprawdzamy, czy jest liczbą całkowitą.
$taskId = filter_input(
    INPUT_POST,
    "task_id",
    FILTER_VALIDATE_INT
);

$userId = $_SESSION["user_id"];

// Niepoprawne ID przerywa operację.
if (!$taskId || $taskId < 1) {
    $_SESSION["task_error"] = "Nieprawidłowy identyfikator zadania.";

    header("Location: dashboard.php");
    exit;
}

// Usuwamy zadanie tylko wtedy, gdy należy do aktualnego użytkownika.
$statement = $pdo->prepare(
    "DELETE FROM tasks
     WHERE id = ? AND user_id = ?"
);

$statement->execute([
    $taskId,
    $userId
]);

// rowCount() mówi, czy faktycznie usunięto jeden rekord.
if ($statement->rowCount() === 1) {
    $_SESSION["task_success"] =
        "Zadanie zostało usunięte.";
} else {
    $_SESSION["task_error"] =
        "Nie znaleziono zadania lub nie masz do niego dostępu.";
}

header("Location: dashboard.php");
exit;