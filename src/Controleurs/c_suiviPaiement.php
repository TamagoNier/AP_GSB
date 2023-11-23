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
        $leVisiteurId = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($leVisiteurId == 'none') {
            foreach ($lesVisiteurs as $visiteur) {
                $nomVisiteur = $pdo->getNomVisiteur($visiteur['id']);
                $date = substr($leMois, 0, 4) . '/' . substr($leMois, 4, 2);
                $montantValide = $pdo->getMontantValide($visiteur['id'], $leMois);
                $montantHorsForfait = $pdo->getSommeMontantFraisHorsForfait($visiteur['id'], $leMois);
                $total = $montantHorsForfait + $montantValide;
                if (!is_null($montantValide)) {
                    include PATH_VIEWS . 'v_tabloFichesFraisVA.php';
                }
            }
        } elseif ($leMois == 'none') {
            foreach ($lesMois as $mois) {
                $nomVisiteur = $pdo->getNomVisiteur($leVisiteurId);
                $date = substr($mois['mois'], 0, 4) . '/' . substr($mois['mois'], 4, 2);
                $montantValide = $pdo->getMontantValide($leVisiteurId, $mois['mois']);
                $montantHorsForfait = $pdo->getSommeMontantFraisHorsForfait($leVisiteurId, $mois['mois']);
                $total = $montantHorsForfait + $montantValide;
                if (!is_null($montantValide)) {
                    include PATH_VIEWS . 'v_tabloFichesFraisVA.php';
                }
            }
        } elseif ($leMois != 'none' && $leVisiteurId != 'none') {
            $nomVisiteur = $pdo->getNomVisiteur($leVisiteurId);
            $date = substr($mois['mois'], 0, 4) . '/' . substr($mois['mois'], 4, 2);
            $total = $montantHorsForfait + $montantValide;
            $montantValide = $pdo->getMontantValide($leVisiteurId, $leMois);
            $montantHorsForfait = $pdo->getSommeMontantFraisHorsForfait($leVisiteurId, $leMois);
            if (!is_null($montantValide)) {
                include PATH_VIEWS . 'v_tabloFichesFraisVA.php';
            }
        } else {
            include PATH_VIEWS . 'v_aucuneFicheFraisVa.php';
        }
        break;
    case 'miseEnPaiement':
        $mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $idVisiteur = filter_input(INPUT_POST, 'idVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $pdo->majEtatFicheFrais($idVisiteur, $mois, 'MP');
        include PATH_VIEWS . 'v_miseEnPaiement.php';
        break;
}

