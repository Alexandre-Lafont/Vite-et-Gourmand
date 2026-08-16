<?php include_once '../templates/header.php'; ?>

    <main class="flex-fill d-flex align-items-center">

        <!-- Login card -->
        <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
            <h1 class="text-center">Connexion</h1>
            <p class="text-center mb-3">
                Si vous ne possédez pas de compte chez Vite & Gourmand, inscrivez-vous en <a href="/pages/register.php" class="text-primary">cliquant ici.</a>
            </p>
            <form>
                <input type="email" placeholder="Adresse e-mail" class="form-control mb-3 rounded-5 border-dark">
                <input type="password" placeholder="Mot de passe" class="form-control mb-3 rounded-5 border-dark">
                <a href="/vite_gourmand/pages/reset-password.php" class="btn btn-link">Réinitialisation du mot de passe</a>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success rounded-5">Se connecter</button>
                </div>
            </form>
        </div>

    </main>

<?php include_once '../templates/footer.php'; ?>