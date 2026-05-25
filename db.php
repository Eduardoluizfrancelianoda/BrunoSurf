<?php
// db.php — conexão centralizada ao banco de dados MySQL
// Altere as credenciais conforme o seu ambiente

define('DB_HOST', 'localhost');
define('DB_NAME', 'brunosurfbd'); // nome do banco de dados
define('DB_USER', 'root');        // usuário do MySQL
define('DB_PASS', '');            // senha do MySQL
define('DB_CHARSET', 'utf8mb4');

/**
 * Retorna uma conexão PDO ao banco de dados.
 * @return PDO
 * @throws PDOException
 */
function conectar(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $opcoes = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
    }
    return $pdo;
}
