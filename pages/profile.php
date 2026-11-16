<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: /vite_gourmand/pages/login.php');
    exit;
}

include_once '../templates/header.php';

include_once '../templates/components/personnal-informations.php';
include_once '../templates/components/orders.php';

if ($_SESSION['role'] === 'employe') {
    include_once '../templates/components/menu-management.php';
    include_once '../templates/components/orders-management.php';
} elseif ($_SESSION['role'] === 'admin') {
    include_once '../templates/components/menu-management.php';
    include_once '../templates/components/orders-management.php';
    include_once '../templates/components/users-management.php';
}

include_once '../templates/footer.php'; 

?>