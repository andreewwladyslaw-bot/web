

<?php

// Główny panel po zalogowaniu. Tutaj użytkownik widzi formularz i swoją listę zadań.

// Najpierw chronimy stronę przed osobą niezalogowaną i łączymy się z bazą.
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";

// Dane zalogowanego użytkownika pobieramy z sesji.
$userId = $_SESSION["user_id"];
$userName = $_SESSION["user_name"];

// Odczytujemy jednorazowe komunikaty po dodaniu, usunięciu lub zmianie zadania.
$taskSuccess = $_SESSION["task_success"] ?? null;
$taskError = $_SESSION["task_error"] ?? null;

// Po odczytaniu kasujemy komunikaty, aby nie pojawiły się drugi raz.
unset($_SESSION["task_success"], $_SESSION["task_error"]);

// Pobieramy z bazy tylko zadania należące do aktualnego użytkownika.
$statement = $pdo->prepare(
    "SELECT
        id,
        title,
        category,
        priority,
        is_important,
        is_completed,
        due_date,
        created_at
     FROM tasks
     WHERE user_id = ?
     ORDER BY is_completed ASC, created_at DESC"
);

$statement->execute([$userId]);

// fetchAll() daje nam tablicę wszystkich znalezionych zadań.
$tasks = $statement->fetchAll();

// Te tablice zamieniają wartości z bazy na ładne napisy dla użytkownika.
$categoryLabels = [
    "programowanie" => "Programowanie",
    "matematyka" => "Matematyka",
    "jezyk_polski" => "Język polski",
    "jezyk_angielski" => "Język angielski",
    "inne" => "Inne"
];

$priorityLabels = [
    "niski" => "Niski",
    "sredni" => "Średni",
    "wysoki" => "Wysoki"
];
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Panel użytkownika — StudyFlow</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Nagłówek pokazuje użytkownika, zegar i przycisk wylogowania. -->
    <header>
        <h1>StudyFlow</h1>

        <p>
            Zalogowany użytkownik:
            <strong>
                <?= htmlspecialchars(
                    $userName,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </strong>
        </p>

        <p>
            Aktualny czas:
            <span id="clock">--:--:--</span>
        </p>

        <form action="logout.php" method="post">
            <button type="submit">Wyloguj się</button>
        </form>
    </header>

    <!-- Główna część panelu: komunikaty, formularz, lista zadań i wskazówka. -->
    <main class="dashboard-content">
        <!-- Komunikat po udanej operacji. JavaScript ukryje go po kilku sekundach. -->
        <?php if ($taskSuccess): ?>
    <p class="auto-hide-message">
        <?= htmlspecialchars(
            $taskSuccess,
            ENT_QUOTES,
            "UTF-8"
        ) ?>
    </p>
<?php endif; ?>

<!-- Komunikat o błędzie wykonanej operacji. -->
<?php if ($taskError): ?>
    <p class="auto-hide-message">
        <?= htmlspecialchars(
            $taskError,
            ENT_QUOTES,
            "UTF-8"
        ) ?>
    </p>
<?php endif; ?>
        <!-- Panel dodawania nowego zadania i filtrowania listy. -->
        <section class="panel task-form-panel">
    <h2>Dodaj nowe zadanie</h2>
            <div>
    <label for="task-filter">
        Filtruj według kategorii:
    </label>

    <!-- Ten select jest obsługiwany przez JavaScript i filtruje karty zadań. -->
    <select id="task-filter">
        <option value="all">Wszystkie kategorie</option>
        <option value="programowanie">Programowanie</option>
        <option value="matematyka">Matematyka</option>
        <option value="jezyk_polski">Język polski</option>
        <option value="jezyk_angielski">Język angielski</option>
        <option value="inne">Inne</option>
    </select>

    <button type="button" id="reset-filter">
        Wyczyść filtr
    </button>
</div>

<p id="filter-message" hidden>
    Brak zadań w wybranej kategorii.
</p>

            <!-- Dane nowego zadania wysyłamy do add_task.php metodą POST. -->
            <form
    action="add_task.php"
    method="post"
    id="task-form"
>
                <div>
                    <label for="title">Nazwa zadania:</label>

                    <input
    type="text"
    id="title"
    name="title"
    minlength="3"
    maxlength="150"
    aria-describedby="title-counter title-error"
    required
>

<small id="title-counter">
    0 / 150 znaków
</small>

<p id="title-error" hidden>
    Nazwa zadania musi mieć co najmniej 3 znaki.
</p>


    Podgląd:
    <strong id="title-preview">Brak nazwy</strong>
    

                    >
                </div>

                <div>
                    <label for="category">Kategoria:</label>

                    <select
                        id="category"
                        name="category"
                        required
                    >
                        <option value="">
                            Wybierz kategorię
                        </option>

                        <option value="programowanie">
                            Programowanie
                        </option>

                        <option value="matematyka">
                            Matematyka
                        </option>

                        <option value="jezyk_polski">
                            Język polski
                        </option>

                        <option value="jezyk_angielski">
                            Język angielski
                        </option>

                        <option value="inne">
                            Inne
                        </option>
                    </select>
                </div>

                <fieldset>
                    <legend>Priorytet:</legend>

                    <label>
                        <input
                            type="radio"
                            name="priority"
                            value="niski"
                        >
                        Niski
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="priority"
                            value="sredni"
                            checked
                        >
                        Średni
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="priority"
                            value="wysoki"
                        >
                        Wysoki
                    </label>
                </fieldset>

                <div>
                    <label>
                        <input
                            type="checkbox"
                            name="is_important"
                            value="1"
                        >
                        To jest ważne zadanie
                    </label>
                </div>

                <div>
                    <label for="due_date">
                        Termin wykonania:
                    </label>

                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                    >
                </div>

                <button
    type="submit"
    id="add-task-button"
    disabled
>
    Dodaj zadanie
</button>
            </form>
        </section>

        <!-- Lista zadań pobranych wcześniej z bazy danych. -->
        <section class="panel task-list-panel">
    <h2>Moje zadania</h2>

    <?php if (empty($tasks)): ?>
        <p>Nie masz jeszcze żadnych zadań.</p>
    <?php else: ?>
        <?php foreach ($tasks as $task): ?>
            <!-- Każda karta przechowuje kategorię w data-category, dzięki czemu JS może ją filtrować. -->
            <article
    class="task-card"
    data-category="<?= htmlspecialchars(
        $task["category"],
        ENT_QUOTES,
        "UTF-8"
    ) ?>"
>
                <h3>
                    <?= htmlspecialchars(
                        $task["title"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </h3>

                <p>
                    Kategoria:
                    <strong>
                        <?= htmlspecialchars(
                            $categoryLabels[$task["category"]]
                                ?? $task["category"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </strong>
                </p>

                <p>
                    Priorytet:
                    <strong>
                        <?= htmlspecialchars(
                            $priorityLabels[$task["priority"]]
                                ?? $task["priority"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </strong>
                </p>

                <p>
                    Ważne zadanie:
                    <strong>
                        <?= (int) $task["is_important"] === 1
                            ? "Tak"
                            : "Nie" ?>
                    </strong>
                </p>

                <p>
                    Termin:
                    <strong>
                        <?php if ($task["due_date"] !== null): ?>
                            <?= htmlspecialchars(
                                $task["due_date"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        <?php else: ?>
                            Brak terminu
                        <?php endif; ?>
                    </strong>
                </p>

                <p>
                    Status:
                    <strong>
                        <?= (int) $task["is_completed"] === 1
                            ? "Wykonane"
                            : "Do wykonania" ?>
                    </strong>
                </p>
                <!-- Ten formularz przełącza status zadania. -->
                <form action="toggle_task.php" method="post">
    <input
        type="hidden"
        name="task_id"
        value="<?= (int) $task["id"] ?>"
    >

    <button type="submit">
        <?= (int) $task["is_completed"] === 1
            ? "Przywróć zadanie"
            : "Oznacz jako wykonane" ?>
    </button>
</form>

<!-- Ten formularz usuwa zadanie. confirm() pyta użytkownika o potwierdzenie. -->
<form
    action="delete_task.php"
    method="post"
    onsubmit="return confirm('Czy na pewno usunąć to zadanie?');"
>
    <input
        type="hidden"
        name="task_id"
        value="<?= (int) $task["id"] ?>"
    >

    <button type="submit">
        Usuń zadanie
    </button>
</form>
            </article>

            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

        <!-- JavaScript losuje tutaj krótką wskazówkę do nauki. -->
        <section class="panel tip-panel">
    <h2>Wskazówka do nauki</h2>

            <p id="study-tip">
                Zacznij od najkrótszego zadania.
            </p>

            <button type="button" id="new-tip">
                Pokaż inną wskazówkę
            </button>
        </section>
    </main>

    <!-- Dołączamy JavaScript odpowiedzialny m.in. za walidację, filtr, losowanie i zegar. -->
    <script src="assets/js/app.js"></script>
</body>
</html>