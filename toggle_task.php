<?php

// Ten plik zmienia status zadania: wykonane <-> do wykonania.

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";

// Zmiana statusu jest dozwolona tylko po wysłaniu formularza POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// Pobieramy i sprawdzamy ID zadania.
$taskId = filter_input(
    INPUT_POST,
    "task_id",
    FILTER_VALIDATE_INT
);

$userId = $_SESSION["user_id"];

// Jeżeli ID jest błędne, wracamy do panelu z komunikatem.
if (!$taskId || $taskId < 1) {
    $_SESSION["task_error"] = "Nieprawidłowy identyfikator zadania.";

    header("Location: dashboard.php");
    exit;
}

// CASE zamienia 1 na 0 albo 0 na 1. Dzięki temu jednym zapytaniem przełączamy status.
$statement = $pdo->prepare(
    "UPDATE tasks
     SET is_completed =
        CASE
            WHEN is_completed = 1 THEN 0
            ELSE 1
        END
     WHERE id = ? AND user_id = ?"
);

$statement->execute([
    $taskId,
    $userId
]);

// Sprawdzamy, czy udało się zmienić dokładnie jedno zadanie.
if ($statement->rowCount() === 1) {
    $_SESSION["task_success"] =
        "Status zadania został zmieniony.";
} else {
    $_SESSION["task_error"] =
        "Nie znaleziono zadania lub nie masz do niego dostępu.";
}

header("Location: dashboard.php");
exit;