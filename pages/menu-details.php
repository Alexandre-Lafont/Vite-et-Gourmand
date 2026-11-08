<?php

include_once '../templates/header.php';

$menuId = filter_input(INPUT_GET, 'menu_id', FILTER_VALIDATE_INT);
$menu = null;
$plats = [];

if ($menuId) {
    $requete = $pdo->prepare(
        'SELECT menu_id, titre, description, nombre_personne_minimum, prix_personne, quantite_restante
        FROM menus
        WHERE menu_id = :menu_id'
    );
    $requete->execute(['menu_id' => $menuId]);
    $menu = $requete->fetch(PDO::FETCH_ASSOC);

    if ($menu) {
        $requetePlats = $pdo->prepare(
            'SELECT plats.titre, plats.regime
            FROM plats
            INNER JOIN menus_plats ON menus_plats.plat_id = plats.plat_id
            WHERE menus_plats.menu_id = :menu_id
            ORDER BY plats.titre'
        );
        $requetePlats->execute(['menu_id' => $menuId]);
        $plats = $requetePlats->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<main class="flex-fill">
    <?php if (!$menu): ?>
        <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
            <h1>Menu introuvable</h1>
            <p>Le menu demandé n'existe pas.</p>
            <a href="/vite_gourmand/pages/list-menus.php" class="btn btn-success rounded-pill mt-3">Retour aux menus</a>
        </div>
    <?php else: ?>
        <?php
            $imageFile = __DIR__ . '/../assets/uploads/' . $menu['titre'] . '.png';
            $imageUrl = '/vite_gourmand/assets/uploads/' . rawurlencode($menu['titre']) . '.png';
        ?>
        <article class="m-3 bg-light rounded-5 overflow-hidden col-md-8 mx-auto">
            <?php if (is_file($imageFile)): ?>
                <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>" class="w-100" style="height: 280px; object-fit: cover;">
            <?php endif; ?>

            <div class="p-3">
                <h1><?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p><?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="border border-top border-secondary-emphasis my-3"></div>
                <p><strong>Nombre minimum de personnes :</strong> <?= (int) $menu['nombre_personne_minimum'] ?></p>
                <p><strong>Prix par personne :</strong> <?= htmlspecialchars($menu['prix_personne'], ENT_QUOTES, 'UTF-8') ?> €</p>
                <p><strong>Quantité restante :</strong> <?= (int) $menu['quantite_restante'] ?></p>

                <h2 class="h4 mt-4">Plats inclus</h2>
                <?php if ($plats): ?>
                    <ul>
                        <?php foreach ($plats as $plat): ?>
                            <li><?= htmlspecialchars($plat['titre'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($plat['regime'], ENT_QUOTES, 'UTF-8') ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun plat renseigné pour ce menu.</p>
                <?php endif; ?>

                <a href="/vite_gourmand/pages/list-menus.php" class="btn btn-outline-success rounded-pill mt-3">Retour aux menus</a>
            </div>
        </article>
    <?php endif; ?>
</main>

<?php include_once '../templates/footer.php'; ?>