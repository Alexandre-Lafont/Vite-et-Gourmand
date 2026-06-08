<?php include_once '../templates/header.php'; ?>

    <main class="flex-fill d-flex align-items-center">

        <!-- Registration card -->
        <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
            <h1 class="text-center">Inscription</h1>
            <p class="text-center mb-3">
                Si vous possédez déjà un compte chez Vite & Gourmand, connectez-vous en <a href="/pages/login.php" class="text-primary">cliquant ici.</a>
            </p>
            <form>
                <input type="name" placeholder="Nom" class="form-control mb-3 rounded-5 border-dark">
                <input type="firstname" placeholder="Prénom" class="form-control mb-3 rounded-5 border-dark">
                <input type="email" placeholder="Adresse e-mail" class="form-control mb-3 rounded-5 border-dark">
                <input type="tel" placeholder="Numéro de téléphone" class="form-control mb-3 rounded-5 border-dark">
                <input type="password" placeholder="Mot de passe" class="form-control mb-3 rounded-5 border-dark">
                <input type="password" placeholder="Confirmer le mot de passe" class="form-control mb-3 rounded-5 border-dark">
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

<?php include_once '../templates/footer.php'; ?>