<?php

include_once __DIR__ . '/../../libs/users.php';

$user = findUserById((int) $_SESSION['user_id']);
if ($user === null) {
    http_response_code(404);
    echo '<main class="flex-fill d-flex align-items-center"><p class="text-danger mx-auto">Utilisateur introuvable.</p></main>';
    return;
}

$profileError = '';
$profileSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $firstname === '' || $email === '' || $phone === '') {
        $profileError = 'Tous les champs personnels sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profileError = 'Veuillez saisir une adresse e-mail valide.';
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $profileError = 'Le numéro de téléphone doit contenir 10 chiffres.';
    } elseif ($password !== '' && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
        $profileError = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
    } elseif ($password !== $confirmPassword) {
        $profileError = 'Les 2 mots de passe doivent être identiques.';
    } else {
        $existingUser = findUserByEmail($email);
        if ($existingUser !== null && (int) $existingUser['user_id'] !== (int) $user['user_id']) {
            $profileError = 'Cette adresse e-mail est déjà utilisée.';
        } else {
            try {
                updateUser(
                    (int) $user['user_id'],
                    $name,
                    $firstname,
                    $email,
                    $phone,
                    $password === '' ? null : $password
                );
                $user = findUserById((int) $user['user_id']);
                $_SESSION['email'] = $email;
                $profileSuccess = 'Vos informations ont été mises à jour.';
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $profileError = 'Cette adresse e-mail est déjà utilisée.';
                } else {
                    throw $exception;
                }
            }
        }
    }
}

?>

<main class="flex-fill d-flex align-items-center">
    <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
        <h1 class="text-center">Mes informations personnelles</h1>

        <?php if ($profileError !== ''): ?>
            <p class="text-danger"><?= htmlspecialchars($profileError, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($profileSuccess !== ''): ?>
            <p class="text-success"><?= htmlspecialchars($profileSuccess, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
            <input
                type="text"
                name="name"
                placeholder="Nom"
                value="<?= htmlspecialchars($_POST['name'] ?? $user['nom'], ENT_QUOTES, 'UTF-8') ?>"
                required
                minlength="2"
                class="form-control mb-3 rounded-5">

            <input
                type="text"
                name="firstname"
                placeholder="Prénom"
                value="<?= htmlspecialchars($_POST['firstname'] ?? $user['prenom'], ENT_QUOTES, 'UTF-8') ?>"
                required
                minlength="2"
                class="form-control mb-3 rounded-5">

            <input
                type="email"
                name="email"
                placeholder="Adresse e-mail"
                value="<?= htmlspecialchars($_POST['email'] ?? $user['email'], ENT_QUOTES, 'UTF-8') ?>"
                required
                class="form-control mb-3 rounded-5">

            <input
                type="tel"
                name="phone"
                placeholder="Numéro de téléphone"
                value="<?= htmlspecialchars($_POST['phone'] ?? $user['telephone'], ENT_QUOTES, 'UTF-8') ?>"
                required
                minlength="10"
                maxlength="10"
                class="form-control mb-3 rounded-5">

            <input
                type="password"
                name="password"
                placeholder="Nouveau mot de passe (facultatif)"
                minlength="8"
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$"
                class="form-control mb-3 rounded-5">

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirmer le nouveau mot de passe"
                class="form-control mb-3 rounded-5">

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-success rounded-5">Enregistrer</button>
            </div>
        </form>
    </div>
</main>