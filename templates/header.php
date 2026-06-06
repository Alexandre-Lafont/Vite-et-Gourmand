<!DOCTYPE html>
<html>
<head>

    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <title>Vite & Gourmand - Accueil</title>

    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel='stylesheet' type='text/css' media='screen' href='style.css'>

</head>

<body class="bg-secondary d-flex flex-column">

    <header class="bg-light"> 
        <div class="px-3 py-2 border-bottom border-dark"> 
            <div class="container"> 
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start"> 
                    <a href="/" class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-black text-decoration-none"> 
                        <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                            <use xlink:href="#bootstrap"></use>
                        </svg> 
                    </a> 
                    <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0"> 
                        <li> <a href="#" class="nav-link text-secondary"> 
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24" aria-hidden="true">
                                    <use xlink:href="#home"></use>
                                </svg>
                                Accueil
                            </a> 
                        </li> 
                        <li> <a href="#" class="nav-link text-black"> 
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24" aria-hidden="true">
                                    <use xlink:href="#speedometer2"></use>
                                </svg>
                                Tableau de bord
                            </a> 
                        </li> 
                        <li> <a href="#" class="nav-link text-black"> 
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24" aria-hidden="true">
                                    <use xlink:href="#table"></use>
                                </svg>
                                Liste des menus
                            </a> 
                        </li> 
                        <li> <a href="#" class="nav-link text-black"> 
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24" aria-hidden="true">
                                    <use xlink:href="#grid"></use>
                                </svg>
                                Panier
                            </a> 
                        </li> 
                        <li> <a href="#" class="nav-link text-black"> 
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24" aria-hidden="true">
                                    <use xlink:href="#people-circle"></use>
                                </svg>
                                Profil
                            </a> 
                        </li> 
                    </ul> 
                </div> 
            </div> 
        </div> 
    </header>

    
    <div class="px-3 py-2 bg-light border-bottom border-dark mb-3 rounded-5 rounded-top-0"> 
        <div class="container d-flex flex-wrap justify-content-center"> 
            <form class="col-12 col-lg-auto mb-2 mb-lg-0 me-lg-auto" role="search"> 
                <input type="search" class="form-control rounded-5" placeholder="Rechercher..." aria-label="Rechercher"> 
            </form> 

            <div class="text-end"> 
                <button type="button" class="btn btn-light text-dark me-2 rounded-5">Se connecter</button> 
                <button type="button" class="btn btn-success rounded-5">S'inscrire</button> 
            </div> 
        </div> 
    </div>

    <main>