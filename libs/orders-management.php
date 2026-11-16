<?php

const ORDER_STATUSES = [
    'accepté',
    'en préparation',
    'en cours de livraison',
    'en attente de retour matériel',
    'terminé',
    'annulée',
];

function findManagedOrders(string $status = '', string $client = ''): array
{
    global $pdo;

    $conditions = [];
    $parameters = [];

    if ($status !== '' && in_array($status, ORDER_STATUSES, true)) {
        $conditions[] = 'commandes.statut = :status';
        $parameters[':status'] = $status;
    }

    if ($client !== '') {
        $conditions[] = '(users.nom LIKE :client OR users.prenom LIKE :client OR users.email LIKE :client)';
        $parameters[':client'] = '%' . $client . '%';
    }

    $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
    $query = $pdo->prepare(
        "SELECT commandes.commande_id, commandes.date_commande, commandes.date_prestation,
                commandes.heure_prestation, commandes.nombre_personne, commandes.adresse_livraison,
                commandes.ville_livraison, commandes.code_postale_livraison, commandes.statut,
                commandes.prix_total, menus.titre, users.nom, users.prenom, users.email,
                users.telephone
         FROM commandes
         INNER JOIN users ON users.user_id = commandes.user_id
         LEFT JOIN menus ON menus.menu_id = commandes.menu_id
         $where
         ORDER BY commandes.date_prestation ASC, commandes.date_commande DESC"
    );
    $query->execute($parameters);

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function findManagedOrder(int $orderId): ?array
{
    global $pdo;

    $query = $pdo->prepare(
        'SELECT commandes.commande_id, commandes.statut, users.email, users.nom, users.prenom
         FROM commandes
         INNER JOIN users ON users.user_id = commandes.user_id
         WHERE commandes.commande_id = :order_id'
    );
    $query->execute([':order_id' => $orderId]);
    $order = $query->fetch(PDO::FETCH_ASSOC);

    return $order ?: null;
}

function updateOrderWithContact(
    int $orderId,
    string $status,
    string $contactMethod,
    string $reason,
    int $contactedBy
): void {
    global $pdo;

    if (!in_array($status, ORDER_STATUSES, true)) {
        throw new InvalidArgumentException('Le statut sélectionné est invalide.');
    }
    if (!in_array($contactMethod, ['email', 'gsm'], true)) {
        throw new InvalidArgumentException('Le mode de contact sélectionné est invalide.');
    }
    if (trim($reason) === '') {
        throw new InvalidArgumentException('Le motif du contact est obligatoire.');
    }

    $order = findManagedOrder($orderId);
    if ($order === null) {
        throw new InvalidArgumentException('La commande sélectionnée est introuvable.');
    }

    $materialReturnTransition = $status === 'en attente de retour matériel' && $order['statut'] !== $status;
    if ($contactMethod === 'email' && !$materialReturnTransition) {
        sendClientContactEmail($order['email'], $orderId, $order['nom'], $order['prenom'], $reason);
    }
    if ($materialReturnTransition) {
        sendMaterialReturnReminder($order['email'], $orderId, $order['nom'], $order['prenom']);
    }

    $pdo->beginTransaction();
    try {
        $contactQuery = $pdo->prepare(
            'INSERT INTO commande_contacts (commande_id, contacte_par, mode_contact, motif)
             VALUES (:order_id, :contacted_by, :contact_method, :reason)'
        );
        $contactQuery->execute([
            ':order_id' => $orderId,
            ':contacted_by' => $contactedBy,
            ':contact_method' => $contactMethod,
            ':reason' => $reason,
        ]);

        $statusQuery = $pdo->prepare(
            'UPDATE commandes
             SET statut = :status
             WHERE commande_id = :order_id'
        );
        $statusQuery->execute([
            ':status' => $status,
            ':order_id' => $orderId,
        ]);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function sendClientContactEmail(
    string $email,
    int $orderId,
    string $lastName,
    string $firstName,
    string $reason
): void {
    $subject = "Vite & Gourmand - Concernant votre commande #$orderId";
    $message = "Bonjour $firstName $lastName,\n\n"
        . "Nous vous contactons au sujet de votre commande #$orderId.\n\n"
        . "Motif : $reason\n\n"
        . "Vous pouvez répondre à cet e-mail pour prendre contact avec notre société.\n\n"
        . "Cordialement,\nVite & Gourmand";
    $headers = "From: noreply@vite-gourmand.local\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n";

    if (!mail($email, $subject, $message, $headers)) {
        throw new RuntimeException("L'e-mail de contact n'a pas pu être envoyé.");
    }
}

function sendMaterialReturnReminder(string $email, int $orderId, string $lastName, string $firstName): void
{
    $subject = 'Vite & Gourmand - Restitution du matériel';
    $message = "Bonjour $firstName $lastName,\n\n"
        . "Votre commande #$orderId est maintenant en attente de retour matériel.\n\n"
        . "Conformément à nos conditions générales de vente, si le matériel n'est pas restitué "
        . "sous 10 jours ouvrés, des frais de 600 euros seront facturés.\n\n"
        . "Pour rendre le matériel, veuillez répondre à cet e-mail afin de prendre contact avec notre société.\n\n"
        . "Cordialement,\nVite & Gourmand";
    $headers = "From: noreply@vite-gourmand.local\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n";

    if (!mail($email, $subject, $message, $headers)) {
        throw new RuntimeException("L'e-mail de retour matériel n'a pas pu être envoyé.");
    }
}
