<?php

    include_once '../libs/config.php';
    include_once '../libs/pdo.php';

?>

<!DOCTYPE html>
<html>
<head>

    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <title>Vite & Gourmand - Accueil</title>

    <link rel="stylesheet" href="/vite_gourmand/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/vite_gourmand/node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
    <link rel='stylesheet' type='text/css' media='screen' href='/vite_gourmand/style.css'>

</head>

<body class="min-vh-100 d-flex flex-column bg-success bg-opacity-50">

    <!-- Détecte la page actuelle -->
    <?php $currentPage = basename($_SERVER['PHP_SELF']);?>

    <header class="bg-light sticky-top border-bottom border-dark rounded-5 rounded-top-0">
        <div class="container py-2">
            <div class="d-flex align-items-center justify-content-between">
                <a href="/vite_gourmand/" class="d-flex align-items-center text-black text-decoration-none">
                    <img src="/vite_gourmand/assets/icons/vite_gourmand_logo.png" alt="Vite & Gourmand" width="40" height="40" class="me-2">
                </a>

                <button class="btn btn-success rounded-pill d-flex align-items-center justify-content-center p-2 border-0"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#mainMenu"
                        aria-expanded="false"
                        aria-controls="mainMenu"
                        aria-label="Ouvrir le menu">
                    <span aria-hidden="true" class="fs-5 lh-1">☰</span>
                </button>
            </div>

            <div class="collapse" id="mainMenu">
                <div class="pt-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                        <a href="/vite_gourmand/" class="nav-link d-flex align-items-center justify-content-center p-2 <?= $currentPage === 'index.php' ? ' active btn btn-success text-white rounded-pill' : 'text-black rounded-pill' ?>"
                            <?= $currentPage === 'index.php' ? 'aria-current="page" ': '' ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-house-door-fill" width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                            </svg>
                        </a>

                        <a href="/vite_gourmand/pages/dashboard.php" class="nav-link d-flex align-items-center justify-content-center p-2 <?= $currentPage === 'dashboard.php' ? ' active btn btn-success text-white rounded-pill' : 'text-black rounded-pill' ?>"
                            <?= $currentPage === 'dashboard.php' ? 'aria-current="page" ': '' ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-speedometer2" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.39.39 0 0 0-.029-.518z"/>
                                <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3"/>
                            </svg>
                        </a>

                        <a href="/vite_gourmand/pages/list-menus.php" class="nav-link d-flex align-items-center justify-content-center p-2 <?= $currentPage === 'list-menus.php' ? ' active btn btn-success text-white rounded-pill' : 'text-black rounded-pill' ?>"
                            <?= $currentPage === 'list-menus.php' ? 'aria-current="page" ': '' ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list-stars" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5"/>
                                <path d="M2.242 2.194a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.28.28 0 0 0-.094.3l.173.569c.078.256-.213.462-.423.3l-.417-.324a.27.27 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.28.28 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.27.27 0 0 0 .259-.194zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.28.28 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.27.27 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.28.28 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.27.27 0 0 0 .259-.194zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.28.28 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.27.27 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.28.28 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.27.27 0 0 0 .259-.194z"/>
                            </svg>
                        </a>

                        <a href="/vite_gourmand/pages/cart.php" class="nav-link d-flex align-items-center justify-content-center p-2 <?= $currentPage === 'cart.php' ? ' active btn btn-success text-white rounded-pill' : 'text-black rounded-pill' ?>"
                            <?= $currentPage === 'cart.php' ? 'aria-current="page" ': '' ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bag-fill" viewBox="0 0 16 16">
                                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z"/>
                            </svg>
                        </a>

                        <a href="/vite_gourmand/pages/profile.php" class="nav-link d-flex align-items-center justify-content-center p-2 <?= $currentPage === 'profile.php' ? ' active btn btn-success text-white rounded-pill' : 'text-black rounded-pill' ?>"
                            <?= $currentPage === 'profile.php' ? 'aria-current="page" ': '' ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg>
                        </a>
                    </div>

                    <form class="mx-auto mt-3" role="search" style="max-width: 500px;">
                        <input type="search" class="form-control rounded-5" placeholder="Rechercher..." aria-label="Rechercher">
                    </form>

                    <div class="d-flex justify-content-center gap-2 mt-3 pb-1">
                        <a href="/vite_gourmand/pages/login.php" class="nav-link d-flex align-items-center <?= $currentPage === 'login.php' ? 'active btn btn-success text-white rounded-pill px-3' : 'text-black rounded-pill px-3' ?>"
                            <?= $currentPage === 'login.php' ? 'aria-current="page" ': '' ?>>
                            Se connecter
                        </a>

                        <a href="/vite_gourmand/pages/register.php" class="nav-link d-flex align-items-center <?= $currentPage === 'register.php' ? 'active btn btn-success text-white rounded-pill px-3' : 'text-black rounded-pill px-3' ?>"
                            <?= $currentPage === 'register.php' ? 'aria-current="page" ': '' ?>>
                            S'inscrire
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>