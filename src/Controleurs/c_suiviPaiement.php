<?php

/**
 * Gestion de l'affichage des frais
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
//$idVisiteur = $_SESSION['idVisiteur'];

switch ($action) {
    case 'suivipaiement':
        $lesVisiteurs = $pdo->getVisiteurs();
        $fichesFraisAValider = $pdo->getFichesFraisAValider();
        include PATH_VIEWS . 'v_filtreVisiteurMois.php';
        break;
    
    case 'afficheTableauFrais':
        $lesVisiteurs = $pdo->getVisiteurs();
        $fichesFraisAValider = $pdo->getFichesFraisAValider();
        include PATH_VIEWS . 'v_filtreVisiteurMois.php';
        
        $leMois = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $leVisiteur = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $montantValide = $pdo->getMontantValide($leVisiteur,$leMois);
        $montantHorsForfait = $pdo->getSommeMontantFraisHorsForfait($leVisiteur,$leMois);
        if(is_null($montantValide)){
            include PATH_VIEWS.'v_aucuneFicheFraisVa.php';
        }
        else{
            include PATH_VIEWS . 'v_tabloFichesFraisVA.php';
        }
        break;
}

