<?php

$query = $pdo->prepare(
    'SELECT commandes.commande_id, commandes.date_commande, commandes.date_prestation,
            commandes.heure_prestation, commandes.nombre_personne, commandes.adresse_livraison,
            commandes.ville_livraison, commandes.code_postale_livraison, commandes.statut,
            commandes.prix_total, menus.titre
     FROM commandes
     LEFT JOIN menus ON menus.menu_id = commandes.menu_id
     WHERE commandes.user_id = :user_id
     ORDER BY commandes.date_prestation DESC, commandes.date_commande DESC'
);
$query->execute([':user_id' => (int) $_SESSION['user_id']]);
$orders = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
    <h2 class="text-center">Mes commandes</h2>

    <?php if ($orders === []): ?>
        <p class="text-center">Aucune commande n'a été passée</p>
        <div class="d-flex justify-content-center">
            <a href="/vite_gourmand/pages/list-menus.php" class="btn btn-outline-success rounded-5 mt-3">Voir les menus</a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($orders as $order): ?>
                <?php
                $isPast = $order['date_prestation'] < date('Y-m-d');
                $orderLabel = $isPast ? 'Commande passée' : 'Commande en cours';
                ?>
                <article class="col-12 col-lg-6">
                    <div class="border border-dark rounded-4 p-3 h-100">
                        <div class="d-flex justify-content-between gap-2">
                            <h3 class="h5">Commande #<?= (int) $order['commande_id'] ?></h3>
                            <span class="badge <?= $isPast ? 'text-bg-secondary' : 'text-bg-success' ?>">
                                <?= htmlspecialchars($orderLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        <p class="mb-1">
                            <strong>Menu :</strong>
                            <?= htmlspecialchars($order['titre'] ?? 'Menu indisponible', ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="mb-1">
                            <strong>Date :</strong>
                            <?= htmlspecialchars($order['date_prestation'], ENT_QUOTES, 'UTF-8') ?>
                            à <?= htmlspecialchars($order['heure_prestation'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="mb-1">
                            <strong>Nombre de personnes :</strong>
                            <?= (int) $order['nombre_personne'] ?>
                        </p>
                        <p class="mb-1">
                            <strong>Statut :</strong>
                            <?= htmlspecialchars($order['statut'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="mb-1">
                            <strong>Livraison :</strong>
                            <?= htmlspecialchars($order['adresse_livraison'], ENT_QUOTES, 'UTF-8') ?>,
                            <?= (int) $order['code_postale_livraison'] ?>
                            <?= htmlspecialchars($order['ville_livraison'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="mb-0">
                            <strong>Total :</strong>
                            <?= number_format((float) $order['prix_total'], 2, ',', ' ') ?> €
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>