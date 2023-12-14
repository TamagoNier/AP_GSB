<?php

/**
 * Gestion des frais
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
$lesVisiteurs = $pdo->getVisiteurs();
require PATH_VIEWS . 'v_validerFrais.php';
switch ($action) {
    case 'afficheFrais':
        $_SESSION['leMois'] = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $_SESSION['leVisiteurId']= filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if($_SESSION['leMois'] == 'none' || $_SESSION['leVisiteurId'] == 'none'){
            include PATH_VIEWS.'v_validerFraisErreur.php';
            break;
        }
        
        elseif ($pdo->estPremierFraisMois($_SESSION['leVisiteurId'], $_SESSION['leMois'])) {
            $pdo->creeNouvellesLignesFrais($_SESSION['leVisiteurId'], $_SESSION['leMois']);
        }
        
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['leVisiteurId'], $_SESSION['leMois']);
        $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['leVisiteurId'], $_SESSION['leMois']);
        $nbJustificatifs = $pdo->getNbjustificatifs($_SESSION['leVisiteurId'], $_SESSION['leMois']);
        
        include PATH_VIEWS . 'v_elementsForfait.php';
        include PATH_VIEWS . 'v_elementsHorsForfait.php';
        break;
    case 'majFraisForfait' :
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (Utilitaires::lesQteFraisValides($lesFrais)) {
            $pdo->majFraisForfait($_SESSION['leVisiteurId'], $_SESSION['leMois'], $lesFrais);
            include PATH_VIEWS.'v_transactionReussie.php';
        } else {
            Utilitaires::ajouterErreur('Les valeurs des frais doivent être numériques');
            include PATH_VIEWS . 'v_erreurs.php';
        }
        break;
    case 'majHorsFraisForfait':
        $id = filter_input(INPUT_POST, 'id', FILTER_DEFAULT, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $date = filter_input(INPUT_POST, 'date', FILTER_DEFAULT, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $libelle = filter_input(INPUT_POST, 'libelle', FILTER_DEFAULT, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_DEFAULT, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        //var_dump($date);
        try {
            include PATH_VIEWS.'v_transactionReussie.php';
        }
        catch (Exception $ex) {
            Utilitaires::ajouterErreur('Données non valide');
            include PATH_VIEWS . 'v_erreurs.php';
        }
        break;
}