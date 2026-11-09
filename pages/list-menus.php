<?php include_once '../templates/header.php'; ?>

    <main class="flex-fill align-items-center">
        <div class="m-3 p-3 col-md-6 mx-auto">
            <h1 class="mt-3">Nos menus</h1>
            <p class="">
                Découvrez nos différents menus traiteurs, préparés avec soin pour toutes vos occasions.
            </p>
        </div>

        <div class="m-3 p-3 col-md-6 mx-auto">
            <?php
                /* Execute la requête SQL : */
                $requete = $pdo->query(
                    'SELECT menus.menu_id, menus.titre, menus.description, menus.nombre_personne_minimum, menus.prix_personne, menus.quantite_restante,
                        /* Concatène les thèmes présents dans le menu en les séparent par ", " */
                        GROUP_CONCAT(DISTINCT themes.libelle ORDER BY themes.libelle SEPARATOR ", ") AS themes 
                    /* Joins les lignes de la table de gauche "menus" avec plats et thèmes */
                    FROM menus
                    LEFT JOIN menus_plats ON menus_plats.menu_id = menus.menu_id
                    LEFT JOIN themes_plats ON themes_plats.plat_id = menus_plats.plat_id
                    LEFT JOIN themes ON themes.theme_id = themes_plats.theme_id
                    /* Regroupe et tri les résultats par menu */ 
                    GROUP BY menus.menu_id, menus.titre, menus.description, menus.nombre_personne_minimum, menus.prix_personne, menus.quantite_restante
                    ORDER BY menus.menu_id'
                );
                /* Récupère tous les résultats sous forme de tableau associatif */
                $menus = $requete->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if ($menus): foreach ($menus as $menu): ?>
                <?php
                    $imageFile = __DIR__ . '/../assets/uploads/' . $menu['titre'] . '.png';
                    $imageUrl = '/vite_gourmand/assets/uploads/' . $menu['titre'] . '.png';
                ?>
                <div class="m-3 bg-light rounded-5 overflow-hidden">
                    <?php if (is_file($imageFile)): ?>
                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>" class="w-100" style="height: 350px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="p-3">
                        <h2><?= $menu['titre'] ?></h2>
                        <p><?= $menu['description'] ?></p>
                        <div class="border border-top border-secondary-emphasis my-3"></div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-people-fill text-success"></i>
                            <p><strong>Nombre minimum de personnes :</strong> <?= $menu['nombre_personne_minimum'] ?></p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-tags-fill text-success"></i>
                            <p><strong>Prix par personne :</strong> <?= $menu['prix_personne'] ?> €</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam text-success"></i>
                            <p><strong>Quantité restante :</strong> <?= $menu['quantite_restante'] ?></p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-fork-knife text-success"></i>
                            <p><strong>Thèmes des plats :</strong>
                                <?= $menu['themes']? $menu['themes']: 'Aucun thème renseigné' ?>
                            </p>
                        </div>
                        <a href="/vite_gourmand/pages/menu-details.php?menu_id=<?= $menu['menu_id'] ?>" class="btn btn-success rounded-pill w-100 mt-3">
                            Voir le menu
                        </a>
                    </div>
                </div>
                <?php endforeach; 
                    else: ?>
                        <p class="m-3 p-3 bg-light rounded-5">Aucun menu disponible.</p>
            <?php endif; ?>
        </div>
    </main>

<?php include_once '../templates/footer.php'; ?>