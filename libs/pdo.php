<!-- Connexion avec la BDD -->

<?php

try {
    /* Retourne les données de .env.local dans un tableau associatif */
    $dbSettings = parse_ini_file(_APP_ENV_);

    $port = $dbSettings['db_port'] ?? 3307;
    $pdo = new PDO("mysql:dbname={$dbSettings["db_name"]};host={$dbSettings["db_host"]};port={$port};charset=utf8", $dbSettings["db_user"], $dbSettings["db_password"]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données" . $e->getMessage());
}

?>