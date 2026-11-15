<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../libs/config.php';
include_once '../libs/pdo.php';
include_once '../libs/users.php';

$emailError = '';
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = findUserByEmail($email);

    if ($user === null) {
        $emailError = 'Cette adresse e-mail est inconnue.';
    } elseif (!password_verify($password, $user['password'])) {
        $passwordError = 'Le mot de passe est invalide.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        header('Location: /vite_gourmand/');
        exit;
    }
}

include_once '../templates/header.php';

?>

    <main class="flex-fill d-flex align-items-center">

        <!-- Login card -->
        <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
            <h1 class="text-center">Connexion</h1>
            <p class="text-center mb-3">
                Si vous ne possédez pas de compte chez Vite & Gourmand, inscrivez-vous en <a href="/vite_gourmand/pages/register.php" class="text-primary">cliquant ici.</a>
            </p>
            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($emailError !== ''): ?>
                    <p class="text-danger mb-1"><?= htmlspecialchars($emailError, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <input type="email" name="email" placeholder="Adresse e-mail" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required class="form-control mb-3 rounded-5 border-dark">
                <?php if ($passwordError !== ''): ?>
                    <p class="text-danger mb-1"><?= htmlspecialchars($passwordError, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <input type="password" name="password" placeholder="Mot de passe" required class="form-control mb-3 rounded-5 border-dark">
                <a href="/vite_gourmand/pages/reset-password.php" class="btn btn-link">Réinitialisation du mot de passe</a>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success rounded-5">Se connecter</button>
                </div>
            </form>
        </div>

    </main>

<?php include_once '../templates/footer.php'; ?>