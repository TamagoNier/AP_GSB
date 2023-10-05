<?php

/**
 * Index du projet GSB
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

use Modeles\PdoGsb;
use Outils\Utilitaires;

require '../vendor/autoload.php';
require '../config/define.php';

session_start();

$pdo = PdoGsb::getPdoGsb();
$estConnecte = Utilitaires::estConnecte();

require PATH_VIEWS . 'v_entete.php';

$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if ($uc && !$estConnecte) {
    $uc = 'connexion';
} elseif (empty($uc)) {
    $uc = 'accueil';
}

/*
switch ($pdo->estComptable($_SESSION['idVisiteur'])) {
        case true :
            echo $_SESSION['idVisiteur'];
            echo 'BONJOUR';
            break;
        case false :
            echo $_SESSION['idVisiteur'];
            echo 'AU REVOIR';
            break;
}*/

$isComptable = $pdo->estComptable($_SESSION['idVisiteur']);

switch ($uc) {
    case 'connexion':
        include PATH_CTRLS . 'c_connexion.php';
        break;
    case 'accueil':
        include PATH_CTRLS . 'c_accueil.php';
        break;
    case 'gererFrais':
        include PATH_CTRLS . 'c_gererFrais.php';
        break;
    case 'etatFrais':
        include PATH_CTRLS . 'c_etatFrais.php';
        break;
    //------
    case 'validerFrais':
        echo 'Redirige vers la page de validation de fiche de frais';
        break;
    case 'suiviPaiement':
        echo 'Redirige vers le suivi de payement';
        break;
    //------
    case 'deconnexion':
        include PATH_CTRLS . 'c_deconnexion.php';
        break;
    default:
        Utilitaires::ajouterErreur('Page non trouvée, veuillez vérifier votre lien...');
        include PATH_VIEWS . 'v_erreurs.php';
        break;
}
require PATH_VIEWS . 'v_pied.php';
