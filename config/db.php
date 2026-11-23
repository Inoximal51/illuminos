<?php
// Подключение к БД MySQLi (OpenServer: root без пароля, БД illuminos).
declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'illuminos';
$DB_PORT = 3306;

// Любая ошибка SQL → исключение
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('[DB] ' . $e->getMessage());
    http_response_code(500);
    exit('Ошибка подключения к БД. Проверьте config/db.php и импорт sql/install.sql.');
}