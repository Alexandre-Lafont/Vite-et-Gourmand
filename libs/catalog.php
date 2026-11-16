<?php

function findMenus(): array
{
    global $pdo;

    $query = $pdo->query(
        'SELECT menu_id, titre, nombre_personne_minimum, prix_personne, description, quantite_restante
         FROM menus
         ORDER BY titre'
    );

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function findMenuWithDishes(int $menuId): ?array
{
    global $pdo;

    $query = $pdo->prepare(
        'SELECT menu_id, titre, nombre_personne_minimum, prix_personne, description, quantite_restante
         FROM menus
         WHERE menu_id = :menu_id'
    );
    $query->execute([':menu_id' => $menuId]);
    $menu = $query->fetch(PDO::FETCH_ASSOC);

    if (!$menu) {
        return null;
    }

    $query = $pdo->prepare('SELECT plat_id FROM menus_plats WHERE menu_id = :menu_id');
    $query->execute([':menu_id' => $menuId]);
    $menu['plat_ids'] = array_map('intval', $query->fetchAll(PDO::FETCH_COLUMN));

    return $menu;
}

function findDishes(): array
{
    global $pdo;

    $query = $pdo->query(
        'SELECT plat_id, titre, regime, photo_path
         FROM plats
         ORDER BY titre'
    );

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function createMenu(
    string $title,
    int $minimumPeople,
    float $price,
    string $description,
    int $remainingQuantity,
    array $dishIds
): void {
    global $pdo;

    $pdo->beginTransaction();
    try {
        $query = $pdo->prepare(
            'INSERT INTO menus (titre, nombre_personne_minimum, prix_personne, description, quantite_restante)
             VALUES (:title, :minimum_people, :price, :description, :remaining_quantity)'
        );
        $query->execute([
            ':title' => $title,
            ':minimum_people' => $minimumPeople,
            ':price' => $price,
            ':description' => $description,
            ':remaining_quantity' => $remainingQuantity,
        ]);

        syncMenuDishes((int) $pdo->lastInsertId(), $dishIds);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function updateMenu(
    int $menuId,
    string $title,
    int $minimumPeople,
    float $price,
    string $description,
    int $remainingQuantity,
    array $dishIds
): void {
    global $pdo;

    $pdo->beginTransaction();
    try {
        $query = $pdo->prepare(
            'UPDATE menus
             SET titre = :title, nombre_personne_minimum = :minimum_people,
                 prix_personne = :price, description = :description,
                 quantite_restante = :remaining_quantity
             WHERE menu_id = :menu_id'
        );
        $query->execute([
            ':menu_id' => $menuId,
            ':title' => $title,
            ':minimum_people' => $minimumPeople,
            ':price' => $price,
            ':description' => $description,
            ':remaining_quantity' => $remainingQuantity,
        ]);

        syncMenuDishes($menuId, $dishIds);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function deleteMenu(int $menuId): void
{
    global $pdo;

    $pdo->beginTransaction();
    try {
        $query = $pdo->prepare('DELETE FROM menus_plats WHERE menu_id = :menu_id');
        $query->execute([':menu_id' => $menuId]);

        $query = $pdo->prepare('DELETE FROM menus WHERE menu_id = :menu_id');
        $query->execute([':menu_id' => $menuId]);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function createDish(string $title, string $diet, string $photoPath): void
{
    global $pdo;

    $query = $pdo->prepare(
        'INSERT INTO plats (titre, regime, photo_path)
         VALUES (:title, :diet, :photo_path)'
    );
    $query->execute([
        ':title' => $title,
        ':diet' => $diet,
        ':photo_path' => $photoPath,
    ]);
}

function updateDish(int $dishId, string $title, string $diet, string $photoPath): void
{
    global $pdo;

    $query = $pdo->prepare(
        'UPDATE plats
         SET titre = :title, regime = :diet, photo_path = :photo_path
         WHERE plat_id = :plat_id'
    );
    $query->execute([
        ':plat_id' => $dishId,
        ':title' => $title,
        ':diet' => $diet,
        ':photo_path' => $photoPath,
    ]);
}

function deleteDish(int $dishId): void
{
    global $pdo;

    $pdo->beginTransaction();
    try {
        foreach (['menus_plats', 'plats_allergenes', 'themes_plats'] as $table) {
            $query = $pdo->prepare("DELETE FROM $table WHERE plat_id = :plat_id");
            $query->execute([':plat_id' => $dishId]);
        }

        $query = $pdo->prepare('DELETE FROM plats WHERE plat_id = :plat_id');
        $query->execute([':plat_id' => $dishId]);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function syncMenuDishes(int $menuId, array $dishIds): void
{
    global $pdo;

    $query = $pdo->prepare('DELETE FROM menus_plats WHERE menu_id = :menu_id');
    $query->execute([':menu_id' => $menuId]);

    $query = $pdo->prepare(
        'INSERT INTO menus_plats (menu_id, plat_id)
         VALUES (:menu_id, :plat_id)'
    );
    foreach (array_unique(array_map('intval', $dishIds)) as $dishId) {
        if ($dishId > 0) {
            $query->execute([
                ':menu_id' => $menuId,
                ':plat_id' => $dishId,
            ]);
        }
    }
}
