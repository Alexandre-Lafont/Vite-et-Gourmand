<!-- Connexion avec la BDD -->

<?php

try {
    /* Retourne les données de .env.local dans un tableau associatif */
    $dbSettings = parse_ini_file(_APP_ENV_);

    $pdo = new PDO("mysql:dbname={$dbSettings["db_name"]};host={$dbSettings["db_host"]};charset=utf8", $dbSettings["db_user"], $dbSettings["db_password"]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données" . $e->getMessage());
}

?>