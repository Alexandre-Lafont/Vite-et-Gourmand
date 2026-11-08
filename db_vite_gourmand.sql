CREATE DATABASE IF NOT EXISTS `vite_gourmand`;
USE `vite_gourmand`;

CREATE TABLE `users`(
    `user_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

    `nom` VARCHAR(50) NOT NULL,
    `prenom` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,

    `role` ENUM('admin', 'employe', 'user') NOT NULL DEFAULT 'user'
);

CREATE TABLE `avis`(
    `avis_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,

    `note` INT NOT NULL CHECK (`note` BETWEEN 0 AND 5),
    `commentaire` TEXT,
    `statut` ENUM('en attente', 'approuve', 'refuse') NOT NULL DEFAULT 'en attente',

    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);

CREATE TABLE `commandes`(
    `commande_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,

    `menu_id` BIGINT NOT NULL,
    `prix_menu` DECIMAL(10, 2) NOT NULL,

    `date_commande` DATETIME NOT NULL,
    `date_prestation` DATE NOT NULL,
    `heure_prestation` VARCHAR(50) NOT NULL,

    `nombre_personne` INT NOT NULL,

    `prix_livraison` DECIMAL(10, 2) NOT NULL,
    `ville_livraison` VARCHAR(50) NOT NULL,
    `code_postale_livraison` INT NOT NULL,
    `adresse_livraison` VARCHAR(100) NOT NULL,
    
    `statut` VARCHAR(50) NOT NULL,
    `pret_materiel` BOOLEAN NOT NULL,
    `restitution_materiel` BOOLEAN NOT NULL,

    `prix_total` DECIMAL(10, 2) NOT NULL,

    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);

CREATE TABLE `menus`(
    `menu_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

    `titre` VARCHAR(50) NOT NULL UNIQUE,
    `nombre_personne_minimum` INT NOT NULL,
    `prix_personne` DECIMAL(10, 2) NOT NULL,
    `description` TEXT NOT NULL,
    `quantite_restante` INT NOT NULL
);

CREATE TABLE `plats`(
    `plat_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

    `titre` VARCHAR(50) NOT NULL UNIQUE,
    `regime` VARCHAR(50) NOT NULL,
    `photo_path` VARCHAR(255) NOT NULL
);

CREATE TABLE `allergenes` (
    allergene_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE `themes` (
    theme_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

    libelle VARCHAR(50) NOT NULL UNIQUE
);

/* commandes <-> menus */
/* N to N */
CREATE TABLE `commandes_menus` (
    commande_id INT NOT NULL,
    menu_id INT NOT NULL,

    PRIMARY KEY (commande_id, menu_id),
    FOREIGN KEY (commande_id) REFERENCES commandes(commande_id),
    FOREIGN KEY (menu_id) REFERENCES menus(menu_id)
);

/* menus <-> plats */
/* N to N */
CREATE TABLE `menus_plats` (
    menu_id INT NOT NULL,
    plat_id INT NOT NULL,

    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menus(menu_id),
    FOREIGN KEY (plat_id) REFERENCES plats(plat_id)
);

/* plats <-> allergenes */
/* N to N */
CREATE TABLE `plats_allergenes` (
    plat_id INT NOT NULL,
    allergene_id INT NOT NULL,

    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plats(plat_id),
    FOREIGN KEY (allergene_id) REFERENCES allergenes(allergene_id)
);

/* themes <-> plats */
/* N to N */
CREATE TABLE `themes_plats` (
    theme_id INT NOT NULL,
    plat_id INT NOT NULL,

    PRIMARY KEY (theme_id, plat_id),
    FOREIGN KEY (theme_id) REFERENCES themes(theme_id),
    FOREIGN KEY (plat_id) REFERENCES plats(plat_id)
);