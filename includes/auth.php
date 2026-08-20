<?php

// Ten plik chroni strony dostępne tylko po zalogowaniu.

// Uruchamiamy sesję tylko wtedy, gdy jeszcze nie jest uruchomiona.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Brak user_id w sesji oznacza, że użytkownik nie jest zalogowany.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}