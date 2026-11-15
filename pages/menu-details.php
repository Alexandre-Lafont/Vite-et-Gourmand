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
            'SELECT plats.titre, plats.regime, plats.photo_path,
                    GROUP_CONCAT(DISTINCT allergenes.libelle ORDER BY allergenes.libelle SEPARATOR ", ") AS allergenes
            FROM plats
            INNER JOIN menus_plats ON menus_plats.plat_id = plats.plat_id
            LEFT JOIN plats_allergenes ON plats_allergenes.plat_id = plats.plat_id
            LEFT JOIN allergenes ON allergenes.allergene_id = plats_allergenes.allergene_id
            WHERE menus_plats.menu_id = :menu_id
            GROUP BY plats.plat_id, plats.titre, plats.regime, plats.photo_path
            ORDER BY plats.titre'
        );
        $requetePlats->execute(['menu_id' => $menuId]);
        $plats = $requetePlats->fetchAll(PDO::FETCH_ASSOC);

        $requeteTheme = $pdo->prepare(
            'SELECT GROUP_CONCAT(DISTINCT themes.libelle ORDER BY themes.libelle SEPARATOR ", ") AS themes
            FROM menus_plats
            INNER JOIN themes_plats ON themes_plats.plat_id = menus_plats.plat_id
            INNER JOIN themes ON themes.theme_id = themes_plats.theme_id
            WHERE menus_plats.menu_id = :menu_id'
        );
        $requeteTheme->execute(['menu_id' => $menuId]);
        $theme = $requeteTheme->fetchColumn() ?: 'Thème non renseigné';
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
            $carouselSlides = [];

            if (is_file($imageFile)) {
                $carouselSlides[] = [
                    'url' => $imageUrl,
                    'alt' => $menu['titre'],
                ];
            }

            foreach ($plats as $plat) {
                $photoName = trim((string) $plat['photo_path']);
                if ($photoName === '') {
                    $photoName = $plat['titre'] . '.png';
                }
                $photoName = basename($photoName);
                $photoFile = __DIR__ . '/../assets/uploads/' . $photoName;

                if ($photoName !== '' && is_file($photoFile)) {
                    $carouselSlides[] = [
                        'url' => '/vite_gourmand/assets/uploads/' . rawurlencode($photoName),
                        'alt' => $plat['titre'],
                    ];
                }
            }
        ?>
        <article class="m-3 bg-light rounded-5 overflow-hidden col-md-8 mx-auto">
            <?php if ($carouselSlides): ?>
                <div id="menu-images-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000" data-bs-wrap="true">
                    <div class="carousel-inner">
                        <?php foreach ($carouselSlides as $index => $slide): ?>
                            <div class="carousel-item<?= $index === 0 ? ' active' : '' ?>">
                                <img src="<?= htmlspecialchars($slide['url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($slide['alt'], ENT_QUOTES, 'UTF-8') ?>" class="d-block w-100" style="height: 280px; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($carouselSlides) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#menu-images-carousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Image précédente</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#menu-images-carousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Image suivante</span>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="p-3">
                <h1><?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>
                    <small class="fs-5 text-success">(<?= htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') ?>)</small>
                </h1>
                <p><?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="border border-top border-secondary-emphasis my-3"></div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-success"></i>
                    <span><strong>Nombre minimum de personnes :</strong> <?= (int) $menu['nombre_personne_minimum'] ?></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-tags-fill text-success"></i>
                    <span><strong>Prix pour <?= (int) $menu['nombre_personne_minimum'] ?> personnes :</strong>
                        <?= number_format((float) $menu['prix_personne'] * (int) $menu['nombre_personne_minimum'], 2, ',', ' ') ?> €
                        <small>(<?= htmlspecialchars($menu['prix_personne'], ENT_QUOTES, 'UTF-8') ?> € par personne)</small>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam text-success"></i>
                    <span><strong>Quantité restante :</strong> <?= (int) $menu['quantite_restante'] ?></span>
                </div>

                <div class="h4 mt-4 d-flex align-items-center gap-2">
                    <i class="bi bi-fork-knife text-success"></i>
                    <span><strong>Plats inclus :</strong></span>
                </div>
                <?php if ($plats): ?>
                    <ul>
                        <?php foreach ($plats as $plat): ?>
                        <li>
                            <?= htmlspecialchars($plat['titre'], ENT_QUOTES, 'UTF-8') ?>
                            (<?= htmlspecialchars($plat['regime'], ENT_QUOTES, 'UTF-8') ?>)
                            — Allergènes :
                            <?= $plat['allergenes'] ? htmlspecialchars($plat['allergenes'], ENT_QUOTES, 'UTF-8') : 'aucun renseigné' ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun plat renseigné pour ce menu.</p>
                <?php endif; ?>

                <div class="alert alert-secondary mt-4" role="note">
                    <strong>Informations pratiques :</strong>
                    <p class="mt-2">Veuillez prévoir un délai de commande adapté à votre prestation. Conservez les plats selon les indications communiquées par Vite &amp; Gourmand et respectez la chaîne du froid.</p>
                </div>

                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="/vite_gourmand/pages/cart.php?menu_id=<?= (int) $menu['menu_id'] ?>" class="btn btn-success rounded-pill mt-3">Commander</a>
                <?php else: ?>
                    <button type="button" class="btn btn-success rounded-pill mt-3" data-bs-toggle="modal" data-bs-target="#login-required-modal">Commander</button>
                <?php endif; ?>
                <a href="/vite_gourmand/pages/list-menus.php" class="btn btn-outline-success rounded-pill mt-3">Retour aux menus</a>
            </div>
        </article>
    <?php endif; ?>
</main>

<?php if ($menu && empty($_SESSION['user_id'])): ?>
    <div class="modal fade" id="login-required-modal" tabindex="-1" aria-labelledby="login-required-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="login-required-modal-title">Connexion nécessaire</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p>Connectez-vous ou créez un compte pour commander un menu.</p>
                </div>
                <div class="modal-footer">
                    <a href="/vite_gourmand/pages/login.php" class="btn btn-success rounded-pill">Se connecter</a>
                    <a href="/vite_gourmand/pages/register.php" class="btn btn-outline-success rounded-pill">S'inscrire</a>
                    <button type="button" class="btn btn-link text-secondary" data-bs-dismiss="modal">Continuer la navigation</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include_once '../templates/footer.php'; ?>