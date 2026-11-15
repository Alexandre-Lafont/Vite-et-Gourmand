<?php

function addUser(string $name, string $firstname, string $email, string $phone, string $password): void
{
    global $pdo;

    $parameters = [
        ':name' => $name,
        ':firstname' => $firstname,
        ':email' => $email,
        ':password' => password_hash($password, PASSWORD_DEFAULT),
    ];

    $telephoneColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'telephone'")->fetch();

    if ($telephoneColumn !== false) {
        $query = $pdo->prepare(
            'INSERT INTO users (nom, prenom, email, telephone, password)
             VALUES (:name, :firstname, :email, :phone, :password)'
        );
        $parameters[':phone'] = $phone;
    } else {
        $query = $pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password)
             VALUES (:name, :firstname, :email, :password)'
        );
    }

    $query->execute($parameters);
}

function findUserByEmail(string $email): ?array
{
    global $pdo;

    $query = $pdo->prepare('SELECT user_id, email, password, role FROM users WHERE email = :email');
    $query->execute([':email' => $email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

?>