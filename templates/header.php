<!DOCTYPE html>
<html>
<head>

    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <title>Vite & Gourmand - Accueil</title>

    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel='stylesheet' type='text/css' media='screen' href='/style.css'>

</head>

<body class="min-vh-100 d-flex flex-column">

    <!-- Détecte la page actuelle -->
    <?php $currentPage = basename($_SERVER['PHP_SELF']);?>

    <header class="bg-light sticky-top border-bottom border-dark rounded-5 rounded-top-0"> 
        <div class="px-3 py-2"> 
            <div class="container"> 
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start"> 

                    <a href="/" class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-black text-decoration-none"> 
                        <svg src="/assets/icons/" alt="" class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                            <use xlink:href="#bootstrap"></use>
                        </svg> 
                    </a> 
                    
                    <ul class="nav col-12 col-lg-auto my-2 justify-content-center align-items-center my-md-0"> 
                        <li> <a href="/" class="nav-link d-flex align-items-center <?= $currentPage === 'index.php' ? 'active btn btn-success text-white rounded-pill' : 'text-black' ?>" 
                                <?= $currentPage === 'index.php' ? 'aria-current="page" ': '' ?>>
                                <svg class="bi me-2" width="16" height="16" aria-hidden="true">
                                    <use href="/assets/icons/house-fill.svg"></use>
                                </svg>
                                Accueil
                            </a> 
                        </li> 

                        <li> <a href="#" class="nav-link  <?= $currentPage === 'dashboard.php' ? 'active btn btn-success text-white rounded-pill' : 'text-black' ?>" 
                                <?= $currentPage === 'dashboard.php' ? 'aria-current="page" ': '' ?>>
                                <svg class="bi me-2" width="16" height="16" aria-hidden="true">
                                    <use href="/assets/icons/speedometer2.svg"></use>
                                </svg>
                                Tableau de bord
                            </a> 
                        </li> 

                        <li> <a href="#" class="nav-link  <?= $currentPage === 'menu.php' ? 'active btn btn-success text-white rounded-pill' : 'text-black' ?>" 
                                <?= $currentPage === 'menu.php' ? 'aria-current="page" ': '' ?>>
                                <svg class="bi me-2" width="16" height="16" aria-hidden="true">
                                    <use href="/assets/icons/card-list.svg"></use>
                                </svg>
                                Liste des menus
                            </a> 
                        </li> 

                        <li> <a href="#" class="nav-link  <?= $currentPage === 'cart.php' ? 'active btn btn-success text-white rounded-pill' : 'text-black' ?>" 
                                <?= $currentPage === 'cart.php' ? 'aria-current="page" ': '' ?>>
                                <svg class="bi me-2" width="16" height="16" aria-hidden="true">
                                    <use href="/assets/icons/bag-fill.svg"></use>
                                </svg>
                                Panier
                            </a> 
                        </li> 

                        <li> <a href="#" class="nav-link  <?= $currentPage === 'profile.php' ? 'active btn btn-success text-white rounded-pill' : 'text-black' ?>" 
                                <?= $currentPage === 'profile.php' ? 'aria-current="page" ': '' ?>>
                                <svg class="bi me-2" width="16" height="16" aria-hidden="true">
                                    <use href="/assets/icons/person-fill.svg"></use>
                                </svg>
                                Profil
                            </a> 
                        </li> 
                    </ul> 
                </div> 
            </div> 
        </div> 
    

    
        <div class="px-3 py-2 "> 
            <div class="container d-flex flex-wrap justify-content-center"> 
                <form class="col-12 col-lg-auto mb-2 mb-lg-0 me-lg-auto" role="search"> 
                    <input type="search" class="form-control rounded-5" placeholder="Rechercher..." aria-label="Rechercher"> 
                </form> 

                <ul class="nav col-12 col-lg-auto my-2 justify-content-center align-items-center my-md-0"> 
                    <li> <a href="/connexion" class="nav-link d-flex align-items-center <?= $currentPage === 'login.php' ? 'active btn btn-success text-white rounded-pill' : 'text-black' ?>" 
                        <?= $currentPage === 'login.php' ? 'aria-current="page" ': '' ?>>
                        Se connecter
                        </a>
                    </li>

                    <li> <a href="/inscription" class="nav-link d-flex align-items-center <?= $currentPage === 'register.php' ? 'active btn btn-success text-white rounded-pill' : 'text-black' ?>" 
                        <?= $currentPage === 'register.php' ? 'aria-current="page" ': '' ?>>
                        S'inscrire
                        </a>
                    </li>
                </ul>
            </div> 
        </div>
    </header>