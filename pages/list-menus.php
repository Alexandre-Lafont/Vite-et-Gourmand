<?php include_once '../templates/header.php'; ?>

    <main class="flex-fill align-items-center">
        <div class="m-3 p-3 bg-light rounded-5 col-md-9 mx-auto">
            <h1 class="">Nos menus</h1>
            <p class="">
                Découvrez nos différents menus traiteurs, préparés avec soin pour toutes vos occasions.
            </p>
        
            <?php
                /* Execute la requête SQL : */
                $requete = $pdo->query(
                    'SELECT menus.menu_id, menus.titre, menus.description, menus.nombre_personne_minimum, menus.prix_personne, menus.quantite_restante,
                        GROUP_CONCAT(DISTINCT themes.libelle ORDER BY themes.libelle SEPARATOR "|") AS themes,
                        GROUP_CONCAT(DISTINCT plats.regime ORDER BY plats.regime SEPARATOR "|") AS regimes
                    /* Joins les lignes de la table de gauche "menus" avec plats et thèmes */
                    FROM menus
                    LEFT JOIN menus_plats ON menus_plats.menu_id = menus.menu_id
                    LEFT JOIN plats ON plats.plat_id = menus_plats.plat_id
                    LEFT JOIN themes_plats ON themes_plats.plat_id = menus_plats.plat_id
                    LEFT JOIN themes ON themes.theme_id = themes_plats.theme_id
                    /* Regroupe et tri les résultats par menu */ 
                    GROUP BY menus.menu_id, menus.titre, menus.description, menus.nombre_personne_minimum, menus.prix_personne, menus.quantite_restante
                    ORDER BY menus.menu_id'
                );
                /* Récupère tous les résultats sous forme de tableau associatif */
                $menus = $requete->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php
                $themesDisponibles = [];
                $regimesDisponibles = [];
                foreach ($menus as $menu) {
                    $themesDisponibles = array_merge($themesDisponibles, $menu['themes'] ? explode('|', $menu['themes']) : []);
                    $regimesDisponibles = array_merge($regimesDisponibles, $menu['regimes'] ? explode('|', $menu['regimes']) : []);
                }
                $themesDisponibles = array_unique($themesDisponibles);
                $regimesDisponibles = array_unique($regimesDisponibles);
                sort($themesDisponibles);
                sort($regimesDisponibles);
            ?>

            <form id="menu-filters" class="bg-light rounded-5 w-100" aria-label="Filtre">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Filtre</h2>
                    <button type="reset" class="btn btn-outline-success rounded-pill">Réinitialiser</button>
                </div>
                <div class="d-flex flex-fill gap-3 flex-wrap">
                    <div class="menu-filter">
                        <button class="menu-filter-toggle btn btn-outline-success rounded-pill d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#max-price-filter" aria-expanded="false" aria-controls="max-price-filter">
                            <span>Prix maximum</span>
                            <i class="bi bi-chevron-down ms-2" aria-hidden="true"></i>
                        </button>
                        <div id="max-price-filter" class="collapse">
                            <label for="max-price" class="form-label">Prix maximum (€)</label>
                            <input type="number" id="max-price" class="form-control" min="0" step="1" placeholder="Ex. 50">
                        </div>
                    </div>

                    <div class="menu-filter">
                        <button class="menu-filter-toggle btn btn-outline-success rounded-pill d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#price-range-filter" aria-expanded="false" aria-controls="price-range-filter">
                            <span>Fourchette de prix</span>
                            <i class="bi bi-chevron-down ms-2" aria-hidden="true"></i>
                        </button>
                        <div id="price-range-filter" class="collapse">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="min-price" class="form-label">Prix minimum (€)</label>
                                    <input type="number" id="min-price" class="form-control" min="0" step="0.01" placeholder="Ex. 20">
                                </div>
                                <div class="col-md-6">
                                    <label for="range-max-price" class="form-label">Prix maximum (€)</label>
                                    <input type="number" id="range-max-price" class="form-control" min="0" step="0.01" placeholder="Ex. 80">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="menu-filter">
                        <button class="menu-filter-toggle btn btn-outline-success rounded-pill d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#theme-filter" aria-expanded="false" aria-controls="theme-filter">
                            <span>Thèmes</span>
                            <i class="bi bi-chevron-down ms-2" aria-hidden="true"></i>
                        </button>
                        <div id="theme-filter" class="collapse">
                            <?php foreach ($themesDisponibles as $index => $theme): ?>
                                <div class="form-check">
                                    <input class="form-check-input theme-checkbox" type="checkbox" value="<?= htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') ?>" id="theme-<?= $index ?>">
                                    <label class="form-check-label" for="theme-<?= $index ?>">
                                        <?= htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="menu-filter">
                        <button class="menu-filter-toggle btn btn-outline-success rounded-pill d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#regime-filter" aria-expanded="false" aria-controls="regime-filter">
                            <span>Régimes</span>
                            <i class="bi bi-chevron-down ms-2" aria-hidden="true"></i>
                        </button>
                        <div id="regime-filter" class="collapse">
                            <?php foreach ($regimesDisponibles as $index => $regime): ?>
                                <div class="form-check">
                                    <input class="form-check-input regime-checkbox" type="checkbox" value="<?= htmlspecialchars($regime, ENT_QUOTES, 'UTF-8') ?>" id="regime-<?= $index ?>">
                                    <label class="form-check-label" for="regime-<?= $index ?>">
                                        <?= htmlspecialchars($regime, ENT_QUOTES, 'UTF-8') ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="menu-filter">
                        <button class="menu-filter-toggle btn btn-outline-success rounded-pill d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#people-filter" aria-expanded="false" aria-controls="people-filter">
                            <span>Nombre de personnes minimum</span>
                            <i class="bi bi-chevron-down ms-2" aria-hidden="true"></i>
                        </button>
                        <div id="people-filter" class="collapse">
                            <label for="min-people" class="form-label">Nombre minimum de personnes</label>
                            <input type="number" id="min-people" class="form-control" min="1" step="1" placeholder="Ex. 10">
                        </div>
                    </div>
                </div>
                <p id="menu-result-count" class="text-muted mt-3 mb-0" aria-live="polite"></p>
            </form>
        </div>

            <div id="menu-list">
            <?php if ($menus): foreach ($menus as $menu): ?>
                <?php
                    $imageFile = __DIR__ . '/../assets/uploads/' . $menu['titre'] . '.png';
                    $imageUrl = '/vite_gourmand/assets/uploads/' . $menu['titre'] . '.png';
                    $menuThemes = $menu['themes'] ? explode('|', $menu['themes']) : [];
                    $menuRegimes = $menu['regimes'] ? explode('|', $menu['regimes']) : [];
                ?>
                <div class="menu-card m-3 bg-light rounded-5 overflow-hidden w-75 mx-auto"
                    data-price="<?= htmlspecialchars($menu['prix_personne'], ENT_QUOTES, 'UTF-8') ?>"
                    data-people="<?= htmlspecialchars($menu['nombre_personne_minimum'], ENT_QUOTES, 'UTF-8') ?>"
                    data-themes="<?= htmlspecialchars(implode('|', $menuThemes), ENT_QUOTES, 'UTF-8') ?>"
                    data-regimes="<?= htmlspecialchars(implode('|', $menuRegimes), ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (is_file($imageFile)): ?>
                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>" class="w-100" style="height: 350px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="p-3">
                        <h2><?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="border border-top border-secondary-emphasis my-3"></div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-people-fill text-success"></i>
                            <p><strong>Nombre minimum de personnes :</strong> <?= $menu['nombre_personne_minimum'] ?></p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-tags-fill text-success"></i>
                            <p><strong>Prix par personne :</strong> <?= htmlspecialchars($menu['prix_personne'], ENT_QUOTES, 'UTF-8') ?> €</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam text-success"></i>
                            <p><strong>Quantité restante :</strong> <?= $menu['quantite_restante'] ?></p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-fork-knife text-success"></i>
                            <p><strong>Thèmes des plats :</strong>
                                <?= $menuThemes ? htmlspecialchars(implode(', ', $menuThemes), ENT_QUOTES, 'UTF-8') : 'Aucun thème renseigné' ?>
                            </p>
                        </div>
                        <a href="/vite_gourmand/pages/menu-details.php?menu_id=<?= $menu['menu_id'] ?>" class="btn btn-success rounded-pill w-100 mt-3">
                            Voir le menu
                        </a>
                        <?php if (!empty($_SESSION['user_id'])): ?>
                            <a href="/vite_gourmand/pages/cart.php?menu_id=<?= (int) $menu['menu_id'] ?>" class="btn btn-outline-success rounded-pill w-100 mt-2">
                                Commander
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-success rounded-pill w-100 mt-2" data-bs-toggle="modal" data-bs-target="#login-required-modal">
                                Commander
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; 
                    else: ?>
                        <p class="m-3 p-3 bg-light rounded-5">Aucun menu disponible.</p>
            <?php endif; ?>
            </div>
            <p id="no-filter-results" class="m-3 p-3 bg-light rounded-5 d-none">Aucun menu ne correspond à vos critères.</p>
        </div>
    </main>

    <?php if (empty($_SESSION['user_id'])): ?>
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

    <script>
        (() => {
            const filterForm = document.getElementById('menu-filters');
            const menuCards = [...document.querySelectorAll('.menu-card')];
            const resultCount = document.getElementById('menu-result-count');
            const noResults = document.getElementById('no-filter-results');

            const selectedValues = (selector) => [...document.querySelectorAll(selector)].filter((input) => input.checked).map((input) => input.value);
            const valuesMatch = (menuValues, selected) => selected.length === 0 || selected.some((value) => menuValues.includes(value));

            const filterMenus = () => {
                const maxPrice = parseFloat(document.getElementById('max-price').value);
                const minPrice = parseFloat(document.getElementById('min-price').value);
                const rangeMaxPrice = parseFloat(document.getElementById('range-max-price').value);
                const minPeople = parseInt(document.getElementById('min-people').value, 10);
                const selectedThemes = selectedValues('.theme-checkbox');
                const selectedRegimes = selectedValues('.regime-checkbox');
                let visibleCount = 0;

                menuCards.forEach((card) => {
                    const price = parseFloat(card.dataset.price);
                    const people = parseInt(card.dataset.people, 10);
                    const themes = card.dataset.themes ? card.dataset.themes.split('|') : [];
                    const regimes = card.dataset.regimes ? card.dataset.regimes.split('|') : [];
                    const matches = (Number.isNaN(maxPrice) || price <= maxPrice)
                        && (Number.isNaN(minPrice) || price >= minPrice)
                        && (Number.isNaN(rangeMaxPrice) || price <= rangeMaxPrice)
                        && (Number.isNaN(minPeople) || people >= minPeople)
                        && valuesMatch(themes, selectedThemes)
                        && valuesMatch(regimes, selectedRegimes);

                    card.classList.toggle('d-none', !matches);
                    if (matches) {
                        visibleCount++;
                    }
                });

                resultCount.textContent = `${visibleCount} menu${visibleCount > 1 ? 's' : ''} correspondant${visibleCount > 1 ? 's' : ''}`;
                noResults.classList.toggle('d-none', visibleCount !== 0);
            };

            filterForm.addEventListener('input', filterMenus);
            filterForm.addEventListener('change', filterMenus);
            filterForm.addEventListener('reset', () => window.setTimeout(filterMenus));
            filterMenus();
        })();
    </script>

<?php include_once '../templates/footer.php'; ?>