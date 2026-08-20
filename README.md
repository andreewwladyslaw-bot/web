# StudyFlow


StudyFlow to prosty szkolny planer zadań wykonany jako projekt kwalifikacyjny
z wykorzystaniem HTML, CSS, JavaScript, PHP oraz SQL.

## Funkcje projektu

Użytkownik może:

* utworzyć konto;
* zalogować się;
* wejść do chronionego panelu;
* dodać zadanie;
* wybrać kategorię zadania;
* wybrać priorytet;
* oznaczyć zadanie jako ważne;
* ustawić termin wykonania;
* wyświetlić własne zadania;
* oznaczyć zadanie jako wykonane;
* przywrócić zadanie do wykonania;
* usunąć zadanie;
* filtrować zadania według kategorii;
* wylogować się.

Każdy użytkownik widzi tylko własne zadania.

## Wykorzystane technologie

* HTML5
* CSS3
* JavaScript
* PHP
* MySQL lub MariaDB
* PDO

## Wymagania

Do uruchomienia projektu potrzebne są:

* XAMPP lub inny lokalny serwer obsługujący PHP i MySQL;
* Apache;
* MySQL lub MariaDB;
* przeglądarka internetowa;
* phpMyAdmin lub inny program do zarządzania bazą danych.

## Instalacja projektu w XAMPP

1. Skopiuj folder `studyflow` do:

   `C:\\xampp\\htdocs\\`

2. Uruchom panel XAMPP.
3. Włącz:

   * Apache;
   * MySQL.
4. Otwórz phpMyAdmin:

   `http://localhost/phpmyadmin`

5. Wybierz zakładkę `Import`.
6. Zaimportuj plik:

   `studyflow/sql/database.sql`

Plik utworzy bazę danych `studyflow\_db` oraz tabele:

* `users`;
* `tasks`.

## Konfiguracja połączenia z bazą

Połączenie znajduje się w pliku:

`config/database.php`

Domyślna konfiguracja XAMPP:

```php
$host = "localhost";
$dbname = "studyflow\_db";
$user = "root";
$password = "";

