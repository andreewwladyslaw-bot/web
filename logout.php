<?php

// Ten plik bezpiecznie wylogowuje użytkownika.

// Otwieramy aktualną sesję, żeby można było ją usunąć.
session_start();

// Wylogowanie wykonujemy tylko po kliknięciu przycisku w formularzu POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// Czyścimy wszystkie dane zapisane w sesji.
$_SESSION = [];

// Jeżeli sesja używa ciasteczka, usuwamy także to ciasteczko z przeglądarki.
if (ini_get("session.use_cookies")) {
    $cookieParameters = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $cookieParameters["path"],
        $cookieParameters["domain"],
        $cookieParameters["secure"],
        $cookieParameters["httponly"]
    );
}

// Na końcu całkowicie niszczymy sesję po stronie serwera.
session_destroy();

header("Location: login.php?logout=1");
exit;