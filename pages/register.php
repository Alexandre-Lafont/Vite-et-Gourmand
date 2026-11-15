<?php

include_once '../templates/header.php';
include_once '../libs/users.php';

$registerError = '';
$emailError = '';
$registerSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($password !== $confirmPassword) {
        $registerError = 'Les 2 mots de passe doivent être identiques.';
    } elseif (findUserByEmail($email) !== null) {
        $emailError = 'Cette adresse e-mail est déjà utilisée.';
    } else {
        try {
            addUser($name, $firstname, $email, $phone, $password);
            $registerSuccess = 'Votre compte a été créé avec succès.';
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $emailError = 'Cette adresse e-mail est déjà utilisée.';
            } else {
                throw $exception;
            }
        }
    }
}

?>

    <main class="flex-fill d-flex align-items-center">

        <!-- Registration card -->
        <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
            <h1 class="text-center">Inscription</h1>
            <p class="text-center mb-3">
                Si vous possédez déjà un compte chez Vite & Gourmand, connectez-vous en <a href="/vite_gourmand/pages/login.php" class="text-primary">cliquant ici.</a>
            </p>
            <p class="mb-3 text-danger">Tous les champs sont obligatoires.</p>

            <!-- La fonction "htmlspecialchars()" convertit les caractères spéciaux en entités HTML (ex: "<" devient "&lt;"), 
            pour prévenir d'attaques XSS.
            Dans ce cas précis, la variable $_SERVER["PHP_SELF"] envoie les données du form à cette page même. 
            De manière à ce que l'utilisateur reçoive un message d'erreur à la même page que le form -->
            <?php if ($registerError !== ''): ?>
                <p class="text-danger"><?= htmlspecialchars($registerError, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if ($registerSuccess !== ''): ?>
                <p class="text-success"><?= htmlspecialchars($registerSuccess, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
                <input 
                    type="text" 
                    name="name" 
                    placeholder="Nom" 
                    required 
                    autocomplete
                    minlength="2"
                    class="form-control mb-3 rounded-5">

                <input 
                    type="text" 
                    name="firstname" 
                    placeholder="Prénom" 
                    required 
                    minlength="2"
                    class="form-control mb-3 rounded-5">

                <?php if ($emailError !== ''): ?>
                    <p class="text-danger mb-1"><?= htmlspecialchars($emailError, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Adresse e-mail" 
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required 
                    class="form-control mb-3 rounded-5">

                <input 
                    type="tel" 
                    name="phone" 
                    placeholder="Numéro de téléphone" 
                    required 
                    minlength="10"
                    maxlength="10"
                    class="form-control mb-3 rounded-5">

                <input 
                    type="password" 
                    id="password"
                    name="password" 
                    placeholder="Mot de passe" 
                    required 
                    minlength="8"
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$"
                    class="form-control mb-3 rounded-5">

                <input 
                    type="password" 
                    id="confirm_password"
                    name="confirm_password" 
                    placeholder="Confirmer le mot de passe" 
                    required 
                    class="form-control mb-3 rounded-5">

                <p>Le mot de passe doit inclure au minimum:</p>
                <ul>
                    <li>8 caractères</li>
                    <li>1 majuscule</li>
                    <li>1 minuscule</li>
                    <li>1 chiffre</li>
                    <li>1 caractère spécial</li>
                </ul>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success rounded-5">S'inscrire</button>
                </div>
            </form>
        </div>

    </main>

    <!-- Vérifie la correspondance des mots de passe -->
    <script>
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        function checkPasswords() {
            if (confirmPassword.value && confirmPassword.value !== password.value) {
                confirmPassword.setCustomValidity('Les mots de passe doivent être identiques.');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }

        password.addEventListener('input', checkPasswords);
        confirmPassword.addEventListener('input', checkPasswords);
    </script>

<?php include_once '../templates/footer.php'; ?>