<?php

include_once __DIR__ . '/../../libs/users.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo '<main class="flex-fill d-flex align-items-center"><p class="text-danger mx-auto">Accès interdit.</p></main>';
    return;
}

if (empty($_SESSION['users_csrf_token'])) {
    $_SESSION['users_csrf_token'] = bin2hex(random_bytes(32));
}

$managementError = '';
$managementSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['users_csrf_token'], $token)) {
        $managementError = 'La requête est invalide. Veuillez réessayer.';
    } else {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        try {
            if ($userId < 1 || findUserById($userId) === null) {
                throw new InvalidArgumentException('L’utilisateur sélectionné est introuvable.');
            }
            if ($userId === (int) $_SESSION['user_id']) {
                throw new InvalidArgumentException('Vous ne pouvez pas modifier ou supprimer votre propre compte.');
            }

            if ($action === 'update_role') {
                updateUserRole($userId, $_POST['role'] ?? '');
                $managementSuccess = 'Le rôle de l’utilisateur a été modifié.';
            } elseif ($action === 'delete_user') {
                deleteUser($userId);
                $managementSuccess = 'L’utilisateur a été supprimé.';
            } else {
                throw new InvalidArgumentException('Action de gestion inconnue.');
            }
        } catch (InvalidArgumentException $exception) {
            $managementError = $exception->getMessage();
        } catch (PDOException $exception) {
            $managementError = $exception->getCode() === '23000'
                ? 'Cet utilisateur ne peut pas être supprimé car il possède des données associées.'
                : 'La modification n’a pas pu être enregistrée.';
        }
    }
}

$users = findUsers();

?>

<main class="flex-fill">
    <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
        <div class="p-4 mx-auto" style="max-width: 700px;">
            <h1 class="text-center mb-4">Gestion des utilisateurs</h1>

            <?php if ($managementError !== ''): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($managementError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($managementSuccess !== ''): ?>
                <div class="alert alert-success"><?= htmlspecialchars($managementSuccess, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($users === []): ?>
                <p class="text-center mb-0">Aucun utilisateur n’est disponible.</p>
            <?php else: ?>
                <label for="userSelector" class="form-label">Utilisateur</label>
                <select id="userSelector" class="form-select mb-4">
                    <option value="">Sélectionner un utilisateur</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= (int) $user['user_id'] ?>">
                            <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['email'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php foreach ($users as $user): ?>
                    <div class="user-actions d-none" data-user-id="<?= (int) $user['user_id'] ?>">
                        <p class="mb-3">
                            <strong>Rôle actuel :</strong>
                            <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?><br>
                            <strong>Téléphone :</strong>
                            <?= htmlspecialchars($user['telephone'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <form method="post" class="border-top pt-3">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['users_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="update_role">
                            <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                            <label for="role-<?= (int) $user['user_id'] ?>" class="form-label">Nouveau rôle</label>
                            <select id="role-<?= (int) $user['user_id'] ?>" name="role" required class="form-select mb-3">
                                <?php foreach (['user' => 'Utilisateur', 'employe' => 'Employé', 'admin' => 'Administrateur'] as $role => $label): ?>
                                    <option value="<?= $role ?>" <?= $user['role'] === $role ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-success rounded-5">Changer le rôle</button>
                        </form>

                        <form method="post" class="border-top mt-3 pt-3" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['users_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                            <button type="submit" class="btn btn-outline-danger rounded-5">Supprimer l’utilisateur</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    const userSelector = document.getElementById('userSelector');
    if (userSelector) {
        const userActions = document.querySelectorAll('.user-actions');
        userSelector.addEventListener('change', function () {
            userActions.forEach(function (actions) {
                actions.classList.toggle('d-none', actions.dataset.userId !== userSelector.value);
            });
        });
    }
</script>
