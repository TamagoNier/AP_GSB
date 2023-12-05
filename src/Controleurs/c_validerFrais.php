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
switch ($action) {
    case 'afficheFrais':
        $leMois = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $leVisiteurId = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if ($pdo->estPremierFraisMois($leVisiteurId, $leMois)) {
            $pdo->creeNouvellesLignesFrais($leVisiteurId, $leMois);
        }
        
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteurId, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteurId, $leMois);
        
        require PATH_VIEWS . 'v_elementsForfait.php';
        break;
}
$lesVisiteurs = $pdo->getVisiteurs();
$fichesFraisAValider = $pdo->getFichesFraisAValider();

include PATH_VIEWS . 'v_validerfrais.php';

