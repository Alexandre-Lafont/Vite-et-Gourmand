<?php

include_once '../templates/header.php';

$menuId = filter_input(INPUT_GET, 'menu_id', FILTER_VALIDATE_INT);
$menu = null;

if ($menuId) {
    $requete = $pdo->prepare(
        'SELECT menu_id, titre
        FROM menus
        WHERE menu_id = :menu_id'
    );
    $requete->execute(['menu_id' => $menuId]);
    $menu = $requete->fetch(PDO::FETCH_ASSOC);
}
?>

<main class="flex-fill">
    <div class="m-3 p-3 bg-light rounded-5 col-12 col-lg-8 mx-auto">
        <h1>Commander</h1>

        <?php if (!$menu): ?>
            <p>Le menu sélectionné est introuvable.</p>
            <a href="/vite_gourmand/pages/list-menus.php" class="btn btn-success rounded-pill mt-3">Retour aux menus</a>
        <?php else: ?>
            <form action="#" method="get" class="mt-4">
                <label for="selected-menu" class="form-label">Menu sélectionné</label>
                <select id="selected-menu" name="menu_id" class="form-select" required>
                    <option value="<?= (int) $menu['menu_id'] ?>" selected>
                        <?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                </select>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php include_once '../templates/footer.php'; ?>
