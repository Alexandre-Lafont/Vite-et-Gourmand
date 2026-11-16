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

    $query = $pdo->prepare('SELECT user_id, nom, prenom, email, telephone, password, role FROM users WHERE email = :email');
    $query->execute([':email' => $email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

function findUserById(int $userId): ?array
{
    global $pdo;

    $query = $pdo->prepare('SELECT user_id, nom, prenom, email, telephone, password, role FROM users WHERE user_id = :user_id');
    $query->execute([':user_id' => $userId]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

function findUsers(): array
{
    global $pdo;

    $query = $pdo->query(
        'SELECT user_id, nom, prenom, email, telephone, role
         FROM users
         ORDER BY nom, prenom'
    );

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function updateUserRole(int $userId, string $role): void
{
    global $pdo;

    if (!in_array($role, ['admin', 'employe', 'user'], true)) {
        throw new InvalidArgumentException('Le rôle sélectionné est invalide.');
    }

    $query = $pdo->prepare('UPDATE users SET role = :role WHERE user_id = :user_id');
    $query->execute([
        ':role' => $role,
        ':user_id' => $userId,
    ]);
}

function deleteUser(int $userId): void
{
    global $pdo;

    $query = $pdo->prepare('DELETE FROM users WHERE user_id = :user_id');
    $query->execute([':user_id' => $userId]);
}

function updateUser(int $userId, string $name, string $firstname, string $email, string $phone, ?string $password = null): void
{
    global $pdo;

    $parameters = [
        ':user_id' => $userId,
        ':name' => $name,
        ':firstname' => $firstname,
        ':email' => $email,
        ':phone' => $phone,
    ];

    $passwordQuery = '';
    if ($password !== null && $password !== '') {
        $passwordQuery = ', password = :password';
        $parameters[':password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $query = $pdo->prepare(
        "UPDATE users
         SET nom = :name, prenom = :firstname, email = :email, telephone = :phone$passwordQuery
         WHERE user_id = :user_id"
    );
    $query->execute($parameters);
}

?>