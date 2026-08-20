// Ten plik dodaje interakcje po stronie przeglądarki.
// Obsługuje formularz, filtr zadań, losową wskazówkę, zegar i automatyczne ukrywanie komunikatów.

// 1. Walidacja nazwy zadania podczas pisania.
// Pobieramy elementy HTML po ich identyfikatorach.
const titleInput = document.querySelector("#title");
const titleCounter = document.querySelector("#title-counter");
const titleError = document.querySelector("#title-error");
const titlePreview = document.querySelector("#title-preview");
const addTaskButton = document.querySelector("#add-task-button");

// Kod uruchamiamy tylko wtedy, gdy wszystkie potrzebne elementy istnieją na stronie.
if (
    titleInput &&
    titleCounter &&
    titleError &&
    titlePreview &&
    addTaskButton
) {
    // Funkcja liczy znaki, pokazuje podgląd i decyduje, czy przycisk ma być aktywny.
    function updateTitleInformation() {
        // value to dokładny tekst wpisany w polu. trim() usuwa spacje z początku i końca.
        const enteredText = titleInput.value;
        const cleanTitle = enteredText.trim();
        const characterCount = enteredText.length;

        // Aktualizujemy licznik znaków widoczny pod polem.
        titleCounter.textContent =
            `${characterCount} / 150 znaków`;

        // Podgląd pokazuje wpisaną nazwę albo tekst „Brak nazwy”.
        if (cleanTitle === "") {
            titlePreview.textContent = "Brak nazwy";
        } else {
            titlePreview.textContent = cleanTitle;
        }

        const titleIsTooShort =
            cleanTitle.length > 0 &&
            cleanTitle.length < 3;

        // hidden=true ukrywa komunikat, a hidden=false go pokazuje.
        titleError.hidden = !titleIsTooShort;

        const titleIsValid =
            cleanTitle.length >= 3 &&
            cleanTitle.length <= 150;

        // Przycisk działa tylko dla poprawnej długości nazwy.
        addTaskButton.disabled = !titleIsValid;
    }

    // Zdarzenie input uruchamia funkcję przy każdym wpisaniu lub usunięciu znaku.
    titleInput.addEventListener(
        "input",
        updateTitleInformation
    );

    updateTitleInformation();
}
// 2. Filtrowanie zadań według kategorii.
const taskFilter = document.querySelector("#task-filter");
const resetFilterButton = document.querySelector("#reset-filter");
const filterMessage = document.querySelector("#filter-message");
const taskCards = document.querySelectorAll(".task-card");

// Funkcja pokazuje tylko te karty, które pasują do wybranej kategorii.
function filterTasks() {
    if (!taskFilter) {
        return;
    }

    const selectedCategory = taskFilter.value;
    let visibleTasks = 0;

    // Przechodzimy po każdej karcie zadania osobno.
    taskCards.forEach(function (taskCard) {
        const taskCategory = taskCard.dataset.category;

        const shouldShow =
            selectedCategory === "all" ||
            taskCategory === selectedCategory;

        // Ukrywamy kartę, jeśli nie pasuje do filtra.
        taskCard.hidden = !shouldShow;

        if (shouldShow) {
            visibleTasks++;
        }
    });

    if (filterMessage) {
        filterMessage.hidden =
            visibleTasks > 0 || taskCards.length === 0;
    }
}

if (taskFilter) {
    // Zdarzenie change działa po wybraniu innej opcji w select.
    taskFilter.addEventListener(
        "change",
        filterTasks
    );
}

if (resetFilterButton && taskFilter) {
    resetFilterButton.addEventListener(
        "click",
        function () {
            taskFilter.value = "all";
            filterTasks();
        }
    );
}
// 3. Losowa wskazówka do nauki.
const studyTip = document.querySelector("#study-tip");
const newTipButton = document.querySelector("#new-tip");

const studyTips = [
    "Zacznij od najkrótszego zadania.",
    "Pracuj przez 25 minut bez rozpraszania się.",
    "Podziel duże zadanie na kilka mniejszych części.",
    "Zakończ jedno zadanie przed rozpoczęciem następnego.",
    "Najpierw wykonaj zadanie o najwyższym priorytecie."
];

// Funkcja wybiera jeden losowy tekst z tablicy studyTips.
function showRandomTip() {
    if (!studyTip) {
        return;
    }

    // Math.random() daje losową liczbę, a Math.floor() zamienia ją na indeks tablicy.
    const randomIndex = Math.floor(
        Math.random() * studyTips.length
    );

    studyTip.textContent = studyTips[randomIndex];
}

if (newTipButton) {
    newTipButton.addEventListener(
        "click",
        showRandomTip
    );
}

showRandomTip();
// ==============================
// 4. Zegar aktualizowany co sekundę
// ==============================

const clock = document.querySelector("#clock");

// Pobieramy aktualną godzinę z komputera użytkownika i wpisujemy ją do elementu #clock.
function updateClock() {
    if (!clock) {
        return;
    }

    const currentDate = new Date();

    clock.textContent = currentDate.toLocaleTimeString(
        "pl-PL",
        {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        }
    );
}

if (clock) {
    updateClock();

    // setInterval uruchamia updateClock co 1000 ms, czyli co 1 sekundę.
    setInterval(updateClock, 1000);
}
// ==============================
// 5. Automatyczne ukrywanie komunikatów po 3 sekundach
// ==============================

const autoHideMessages = document.querySelectorAll(
    ".auto-hide-message"
);

// Dla każdego komunikatu ustawiamy osobny licznik czasu.
autoHideMessages.forEach(function (message) {
    // setTimeout wykona kod tylko raz po podanym czasie.
    setTimeout(function () {
        message.hidden = true;
    }, 3000);
});