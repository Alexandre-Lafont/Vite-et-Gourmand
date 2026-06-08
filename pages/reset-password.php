<?php include_once '../templates/header.php'; ?>

    <main class="flex-fill d-flex align-items-center">

        <!-- Registration card -->
        <div class="m-3 p-3 bg-light rounded-5 col-md-6 mx-auto">
            <h1 class="text-center">Réinitialisation du mot de passe</h1>
            <p class="text-center mb-3">
                Pour réinitialiser le mot de passe, veuillez renseignez votre adresse e-mail dans le champ si dessous:
            </p>
            <form>
                <input type="email" placeholder="Adresse e-mail" class="form-control mb-3 rounded-5 border-dark">
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success rounded-5">Envoyer</button>
                </div>
            </form>
        </div>

    </main>

<?php include_once '../templates/footer.php'; ?>