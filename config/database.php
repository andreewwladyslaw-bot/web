<?php

// Ten plik tworzy połączenie PHP z bazą MySQL.
// Inne pliki dołączają go przez require_once i korzystają ze zmiennej $pdo.

// Dane potrzebne do połączenia z bazą.
$host = "localhost";
$dbname = "studyflow_db";
$user = "root";
$password = "";

// Próbujemy połączyć się z bazą danych.
try {
    // Obiekt PDO pozwala wykonywać bezpieczne zapytania SQL.
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Jeżeli połączenie się nie uda, zatrzymujemy stronę i pokazujemy błąd.
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}