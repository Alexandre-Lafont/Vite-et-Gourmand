<?php include_once '../templates/header.php'; 

$registerError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $registerError = 'Les 2 mots de passe doivent être identiques.';
    }
}

?>

    <main class="flex-fill d-flex align-items-center">

        <!-- Registration card -->
        <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
            <h1 class="text-center">Inscription</h1>
            <p class="text-center mb-3">
                Si vous possédez déjà un compte chez Vite & Gourmand, connectez-vous en <a href="/pages/login.php" class="text-primary">cliquant ici.</a>
            </p>
            <p class="mb-3 text-danger">Tous les champs sont obligatoires.</p>

            <!-- La fonction "htmlspecialchars()" convertit les caractères spéciaux en entités HTML (ex: "<" devient "&lt;"), 
            pour prévenir d'attaques XSS.
            Dans ce cas précis, la variable $_SERVER["PHP_SELF"] envoie les données du form à cette page même. 
            De manière à ce que l'utilisateur reçoive un message d'erreur à la même page que le form -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
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

                <input 
                    type="email" 
                    name="email" 
                    placeholder="Adresse e-mail" 
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
                <a href="/pages/reset-password.php" class="btn btn-link">Réinitialisation du mot de passe</a>
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