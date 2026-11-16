<?php

include_once __DIR__ . '/../../libs/orders-management.php';

if (!in_array($_SESSION['role'] ?? '', ['admin', 'employe'], true)) {
    http_response_code(403);
    echo '<main class="flex-fill d-flex align-items-center"><p class="text-danger mx-auto">Accès interdit.</p></main>';
    return;
}

if (empty($_SESSION['orders_csrf_token'])) {
    $_SESSION['orders_csrf_token'] = bin2hex(random_bytes(32));
}

$statusFilter = $_GET['status'] ?? '';
$clientFilter = trim($_GET['client'] ?? '');
$managementError = '';
$managementSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['orders_csrf_token'], $token)) {
        $managementError = 'La requête est invalide. Veuillez réessayer.';
    } else {
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $contactMethod = $_POST['contact_method'] ?? '';
        $reason = trim($_POST['reason'] ?? '');
        $newStatus = $action === 'cancel' ? 'annulée' : ($_POST['status'] ?? '');

        try {
            if ($action !== 'update_status' && $action !== 'cancel') {
                throw new InvalidArgumentException('Action de gestion inconnue.');
            }
            updateOrderWithContact(
                $orderId,
                $newStatus,
                $contactMethod,
                $reason,
                (int) $_SESSION['user_id']
            );
            $managementSuccess = $action === 'cancel'
                ? 'La commande a été annulée après enregistrement du contact.'
                : 'Le statut de la commande a été modifié après enregistrement du contact.';
        } catch (InvalidArgumentException | RuntimeException $exception) {
            $managementError = $exception->getMessage();
        } catch (PDOException $exception) {
            $managementError = 'La commande n’a pas pu être mise à jour.';
        }
    }
}

$orders = findManagedOrders($statusFilter, $clientFilter);

?>

<main class="flex-fill">
    <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto container py-4">
        <h1 class="text-center mb-4">Gestion des commandes</h1>

        <?php if ($managementError !== ''): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($managementError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($managementSuccess !== ''): ?>
            <div class="alert alert-success"><?= htmlspecialchars($managementSuccess, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="get" class="p-3 mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label for="statusFilter" class="form-label">Statut</label>
                    <select id="statusFilter" name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <?php foreach (ORDER_STATUSES as $status): ?>
                            <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= $statusFilter === $status ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label for="clientFilter" class="form-label">Client</label>
                    <input id="clientFilter" type="search" name="client" value="<?= htmlspecialchars($clientFilter, ENT_QUOTES, 'UTF-8') ?>" placeholder="Nom, prénom ou e-mail" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-success rounded-5 w-100">Filtrer</button>
                </div>
            </div>
        </form>

        <?php if ($orders === []): ?>
            <div class="alert alert-secondary">Aucune commande ne correspond aux filtres.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($orders as $order): ?>
                    <article class="col-12 col-xl-6">
                        <div class="bg-light rounded-4 p-3 h-100">
                            <h2 class="h5">Commande #<?= (int) $order['commande_id'] ?></h2>
                            <p class="mb-1">
                                <strong>Client :</strong>
                                <?= htmlspecialchars($order['prenom'] . ' ' . $order['nom'], ENT_QUOTES, 'UTF-8') ?>
                                (<?= htmlspecialchars($order['email'], ENT_QUOTES, 'UTF-8') ?>)
                            </p>
                            <p class="mb-1"><strong>Téléphone :</strong> <?= htmlspecialchars($order['telephone'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-1"><strong>Menu :</strong> <?= htmlspecialchars($order['titre'] ?? 'Menu indisponible', ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-1"><strong>Date de prestation :</strong> <?= htmlspecialchars($order['date_prestation'], ENT_QUOTES, 'UTF-8') ?> à <?= htmlspecialchars($order['heure_prestation'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-3"><strong>Statut actuel :</strong> <?= htmlspecialchars($order['statut'], ENT_QUOTES, 'UTF-8') ?></p>

                            <form method="post" class="border-top pt-3">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['orders_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="order_id" value="<?= (int) $order['commande_id'] ?>">
                                <label for="status-<?= (int) $order['commande_id'] ?>" class="form-label">Nouveau statut</label>
                                <select id="status-<?= (int) $order['commande_id'] ?>" name="status" class="form-select mb-2">
                                    <?php foreach (ORDER_STATUSES as $status): ?>
                                        <?php if ($status !== 'annulée'): ?>
                                            <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= $order['statut'] === $status ? 'selected' : '' ?>>
                                                <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <label for="contact-<?= (int) $order['commande_id'] ?>" class="form-label">Mode de contact</label>
                                <select id="contact-<?= (int) $order['commande_id'] ?>" name="contact_method" required class="form-select mb-2">
                                    <option value="">Sélectionner</option>
                                    <option value="email">E-mail</option>
                                    <option value="gsm">GSM</option>
                                </select>
                                <label for="reason-<?= (int) $order['commande_id'] ?>" class="form-label">Motif du contact / de l'annulation</label>
                                <textarea id="reason-<?= (int) $order['commande_id'] ?>" name="reason" required class="form-control mb-2"></textarea>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" name="action" value="update_status" class="btn btn-success rounded-5">Modifier le statut</button>
                                    <button type="submit" name="action" value="cancel" class="btn btn-outline-danger rounded-5" formnovalidate onclick="return confirm('Annuler cette commande après contact du client ?');">Annuler la commande</button>
                                </div>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
