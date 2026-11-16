<?php

include_once __DIR__ . '/../../libs/catalog.php';

if (!in_array($_SESSION['role'] ?? '', ['admin', 'employe'], true)) {
    http_response_code(403);
    echo '<main class="flex-fill d-flex align-items-center"><p class="text-danger mx-auto">Accès interdit.</p></main>';
    return;
}

if (empty($_SESSION['catalog_csrf_token'])) {
    $_SESSION['catalog_csrf_token'] = bin2hex(random_bytes(32));
}

$managementError = '';
$managementSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['catalog_csrf_token'], $token)) {
        $managementError = 'La requête est invalide. Veuillez réessayer.';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'create_menu' || $action === 'update_menu') {
                $menuId = (int) ($_POST['menu_id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $minimumPeople = filter_var($_POST['minimum_people'] ?? null, FILTER_VALIDATE_INT);
                $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
                $description = trim($_POST['description'] ?? '');
                $remainingQuantity = filter_var($_POST['remaining_quantity'] ?? null, FILTER_VALIDATE_INT);
                $dishIds = is_array($_POST['dish_ids'] ?? null) ? $_POST['dish_ids'] : [];

                if (
                    $title === '' || $description === '' || $minimumPeople === false || $minimumPeople < 1
                    || $price === false || $price < 0 || $remainingQuantity === false || $remainingQuantity < 0
                ) {
                    throw new InvalidArgumentException('Veuillez renseigner correctement tous les champs du menu.');
                }

                if ($action === 'create_menu') {
                    createMenu($title, $minimumPeople, $price, $description, $remainingQuantity, $dishIds);
                    $managementSuccess = 'Le menu a été ajouté.';
                } else {
                    if ($menuId < 1 || findMenuWithDishes($menuId) === null) {
                        throw new InvalidArgumentException('Le menu sélectionné est introuvable.');
                    }
                    updateMenu($menuId, $title, $minimumPeople, $price, $description, $remainingQuantity, $dishIds);
                    $managementSuccess = 'Le menu a été modifié.';
                }
            } elseif ($action === 'delete_menu') {
                $menuId = (int) ($_POST['menu_id'] ?? 0);
                if ($menuId < 1) {
                    throw new InvalidArgumentException('Le menu sélectionné est invalide.');
                }
                deleteMenu($menuId);
                $managementSuccess = 'Le menu a été supprimé.';
            } elseif ($action === 'create_dish' || $action === 'update_dish') {
                $dishId = (int) ($_POST['dish_id'] ?? 0);
                $title = trim($_POST['dish_title'] ?? '');
                $diet = trim($_POST['diet'] ?? '');
                $photoPath = trim($_POST['photo_path'] ?? '');

                if ($title === '' || $diet === '' || $photoPath === '') {
                    throw new InvalidArgumentException('Veuillez renseigner tous les champs du plat.');
                }

                if ($action === 'create_dish') {
                    createDish($title, $diet, $photoPath);
                    $managementSuccess = 'Le plat a été ajouté.';
                } else {
                    if ($dishId < 1) {
                        throw new InvalidArgumentException('Le plat sélectionné est invalide.');
                    }
                    updateDish($dishId, $title, $diet, $photoPath);
                    $managementSuccess = 'Le plat a été modifié.';
                }
            } elseif ($action === 'delete_dish') {
                $dishId = (int) ($_POST['dish_id'] ?? 0);
                if ($dishId < 1) {
                    throw new InvalidArgumentException('Le plat sélectionné est invalide.');
                }
                deleteDish($dishId);
                $managementSuccess = 'Le plat a été supprimé.';
            } else {
                throw new InvalidArgumentException('Action de gestion inconnue.');
            }
        } catch (InvalidArgumentException $exception) {
            $managementError = $exception->getMessage();
        } catch (PDOException $exception) {
            $managementError = $exception->getCode() === '23000'
                ? 'Un menu ou un plat portant ce titre existe déjà.'
                : 'La modification n’a pas pu être enregistrée.';
        }
    }
}

$menus = findMenus();
$dishes = findDishes();

?>

<main class="flex-fill">
    <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
        <div class="p-4 mx-auto" style="max-width: 700px;">
            <h1 class="text-center mb-4">Gestion des menus et des plats</h1>

            <?php if ($managementError !== ''): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($managementError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($managementSuccess !== ''): ?>
                <div class="alert alert-success"><?= htmlspecialchars($managementSuccess, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="d-grid gap-3 ">
                <button type="button" class="btn btn-outline-success rounded-5 w-50 d-block mx-auto" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                    Ajouter un menu
                </button>
                <button type="button" class="btn btn-outline-success rounded-5 w-50 d-block mx-auto" data-bs-toggle="modal" data-bs-target="#editMenuModal">
                    Modifier un menu
                </button>
                <button type="button" class="btn btn-outline-success rounded-5 w-50 d-block mx-auto" data-bs-toggle="modal" data-bs-target="#addDishModal">
                    Ajouter un plat
                </button>
                <button type="button" class="btn btn-outline-success rounded-5 w-50 d-block mx-auto" data-bs-toggle="modal" data-bs-target="#editDishModal">
                    Modifier un plat
                </button>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="addMenuModalLabel">Ajouter un menu</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['catalog_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="create_menu">
                    <input type="text" name="title" placeholder="Titre" required maxlength="50" class="form-control mb-2">
                    <input type="number" name="minimum_people" placeholder="Nombre minimum de personnes" required min="1" class="form-control mb-2">
                    <input type="number" name="price" placeholder="Prix par personne" required min="0" step="0.01" class="form-control mb-2">
                    <textarea name="description" placeholder="Description" required class="form-control mb-2"></textarea>
                    <input type="number" name="remaining_quantity" placeholder="Quantité restante" required min="0" class="form-control mb-2">
                    <?php if ($dishes !== []): ?>
                        <p class="mb-1">Plats associés :</p>
                        <?php foreach ($dishes as $dish): ?>
                            <label class="d-block">
                                <input type="checkbox" name="dish_ids[]" value="<?= (int) $dish['plat_id'] ?>">
                                <?= htmlspecialchars($dish['titre'], ENT_QUOTES, 'UTF-8') ?>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success rounded-5">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="editMenuModalLabel">Modifier un menu</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <?php if ($menus === []): ?>
                <div class="modal-body">Aucun menu n'est disponible.</div>
            <?php else: ?>
                <form method="post" id="editMenuForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['catalog_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="action" value="update_menu">
                        <label for="menuSelector" class="form-label">Menu à modifier</label>
                        <select id="menuSelector" name="menu_id" class="form-select mb-3" required>
                            <option value="">Sélectionner un menu</option>
                            <?php foreach ($menus as $menu): ?>
                                <option value="<?= (int) $menu['menu_id'] ?>"><?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php foreach ($menus as $menu): ?>
                            <?php $menuDetails = findMenuWithDishes((int) $menu['menu_id']); ?>
                            <div class="menu-edit-fields d-none" data-menu-id="<?= (int) $menu['menu_id'] ?>">
                                <input type="text" name="title" value="<?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="50" class="form-control mb-2">
                                <input type="number" name="minimum_people" value="<?= (int) $menu['nombre_personne_minimum'] ?>" required min="1" class="form-control mb-2">
                                <input type="number" name="price" value="<?= htmlspecialchars($menu['prix_personne'], ENT_QUOTES, 'UTF-8') ?>" required min="0" step="0.01" class="form-control mb-2">
                                <textarea name="description" required class="form-control mb-2"><?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                <input type="number" name="remaining_quantity" value="<?= (int) $menu['quantite_restante'] ?>" required min="0" class="form-control mb-2">
                                <?php foreach ($dishes as $dish): ?>
                                    <label class="d-block">
                                        <input
                                            type="checkbox"
                                            name="dish_ids[]"
                                            value="<?= (int) $dish['plat_id'] ?>"
                                            <?= in_array((int) $dish['plat_id'], $menuDetails['plat_ids'], true) ? 'checked' : '' ?>>
                                        <?= htmlspecialchars($dish['titre'], ENT_QUOTES, 'UTF-8') ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success rounded-5">Enregistrer</button>
                    </div>
                </form>
                <form method="post" id="deleteMenuForm" onsubmit="return confirm('Supprimer ce menu ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['catalog_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="delete_menu">
                    <input type="hidden" name="menu_id" id="deleteMenuId">
                    <div class="modal-footer pt-0">
                        <button type="submit" class="btn btn-outline-danger rounded-5">Supprimer le menu sélectionné</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="addDishModal" tabindex="-1" aria-labelledby="addDishModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="addDishModalLabel">Ajouter un plat</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['catalog_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="create_dish">
                    <input type="text" name="dish_title" placeholder="Titre" required maxlength="50" class="form-control mb-2">
                    <input type="text" name="diet" placeholder="Régime alimentaire" required maxlength="50" class="form-control mb-2">
                    <input type="text" name="photo_path" placeholder="Chemin de la photo" required maxlength="255" class="form-control mb-2">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success rounded-5">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editDishModal" tabindex="-1" aria-labelledby="editDishModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="editDishModalLabel">Modifier un plat</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <?php if ($dishes === []): ?>
                <div class="modal-body">Aucun plat n'est disponible.</div>
            <?php else: ?>
                <form method="post" id="editDishForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['catalog_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="action" value="update_dish">
                        <label for="dishSelector" class="form-label">Plat à modifier</label>
                        <select id="dishSelector" name="dish_id" class="form-select mb-3" required>
                            <option value="">Sélectionner un plat</option>
                            <?php foreach ($dishes as $dish): ?>
                                <option value="<?= (int) $dish['plat_id'] ?>"><?= htmlspecialchars($dish['titre'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php foreach ($dishes as $dish): ?>
                            <div class="dish-edit-fields d-none" data-dish-id="<?= (int) $dish['plat_id'] ?>">
                                <input type="text" name="dish_title" value="<?= htmlspecialchars($dish['titre'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="50" class="form-control mb-2">
                                <input type="text" name="diet" value="<?= htmlspecialchars($dish['regime'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="50" class="form-control mb-2">
                                <input type="text" name="photo_path" value="<?= htmlspecialchars($dish['photo_path'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="255" class="form-control mb-2">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success rounded-5">Enregistrer</button>
                    </div>
                </form>
                <form method="post" id="deleteDishForm" onsubmit="return confirm('Supprimer ce plat ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['catalog_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="delete_dish">
                    <input type="hidden" name="dish_id" id="deleteDishId">
                    <div class="modal-footer pt-0">
                        <button type="submit" class="btn btn-outline-danger rounded-5">Supprimer le plat sélectionné</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function setupManagementSelector(selectorId, fieldsSelector, deleteInputId) {
        const selector = document.getElementById(selectorId);
        if (!selector) {
            return;
        }

        const fields = document.querySelectorAll(fieldsSelector);
        const deleteInput = document.getElementById(deleteInputId);

        selector.addEventListener('change', function () {
            fields.forEach(function (field) {
                const isSelected = field.dataset.menuId === selector.value || field.dataset.dishId === selector.value;
                field.classList.toggle('d-none', !isSelected);
                field.querySelectorAll('input, textarea').forEach(function (input) {
                    input.disabled = !isSelected;
                });
            });
            if (deleteInput) {
                deleteInput.value = selector.value;
            }
        });
    }

    setupManagementSelector('menuSelector', '.menu-edit-fields', 'deleteMenuId');
    setupManagementSelector('dishSelector', '.dish-edit-fields', 'deleteDishId');
</script>