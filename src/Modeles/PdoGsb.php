<?php

/**
 * Classe d'accès aux données.
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */
/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $connexion de type PDO
 * $instance qui contiendra l'unique instance de la classe
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

namespace Modeles;

use PDO;
use Outils\Utilitaires;

require '../config/bdd.php';

class PdoGsb {

    protected $connexion;
    private static $instance = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() {
        $this->connexion = new PDO(DB_DSN, DB_USER, DB_PWD);
        $this->connexion->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct() {
        $this->connexion = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb(): PdoGsb {
        if (self::$instance == null) {
            self::$instance = new PdoGsb();
        }
        return self::$instance;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT visiteur.id AS id, visiteur.nom AS nom, '
                . 'visiteur.prenom AS prenom '
                . 'FROM visiteur '
                . 'WHERE visiteur.login = :unLogin'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->execute();
        $response = $requetePrepare->fetch();
        if (is_array($response)) {
            return $response;
        } else {
            return array();
        }
    }

    /**
     * Retourne le hash du mot de passe de l'utilisateur si 
     * ce dernier est present dans la bdd
     * 
     * @param type $login
     * @return type
     */
    public function getMdpVisiteur($login) {
        $requetePrepare = $this->connexion->prepare(
                'SELECT mdp '
                . 'FROM visiteur '
                . 'WHERE visiteur.login = :unLogin'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch(PDO::FETCH_OBJ)->mdp;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = Utilitaires::dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois): int {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fraisforfait.id as idfrais, '
                . 'fraisforfait.libelle as libelle, '
                . 'fraisforfait.montant as montant, '
                . 'lignefraisforfait.quantite as quantite '
                . 'FROM lignefraisforfait '
                . 'INNER JOIN fraisforfait '
                . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais(): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fraisforfait.id as idfrais '
                . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais): void {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = $this->connexion->prepare(
                    'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs): void {
        $requetePrepare = $this->connexion->prepare(
                'UPDATE fichefrais '
                . 'SET nbjustificatifs = :unNbJustificatifs '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
                ':unNbJustificatifs',
                $nbJustificatifs,
                PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois): bool {
        $boolReturn = false;
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.mois FROM fichefrais '
                . 'WHERE fichefrais.mois = :unMois '
                . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur): string {
        $requetePrepare = $this->connexion->prepare(
                'SELECT MAX(mois) as dernierMois '
                . 'FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois): void {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = $this->connexion->prepare(
                'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
                . 'montantvalide,datemodif,idetat) '
                . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = $this->connexion->prepare(
                    'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                    . 'idfraisforfait,quantite) '
                    . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais['idfrais'], PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant): void {
        $dateFr = Utilitaires::dateFrancaisVersAnglais($date);
        $requetePrepare = $this->connexion->prepare(
                'INSERT INTO lignefraishorsforfait '
                . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
                . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais): void {
        $requetePrepare = $this->connexion->prepare(
                'DELETE FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.mois AS mois FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.idetat as idEtat, '
                . 'fichefrais.datemodif as dateModif,'
                . 'fichefrais.nbjustificatifs as nbJustificatifs, '
                . 'fichefrais.montantvalide as montantValide, '
                . 'etat.libelle as libEtat '
                . 'FROM fichefrais '
                . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat): void {
        $requetePrepare = $this->connexion->prepare(
                'UPDATE fichefrais '
                . 'SET idetat = :unEtat, datemodif = now() '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    //-------------------------------------------------------------------------------------
    public function estComptable($idVisiteur): bool {
        $requetePrepare = $this->connexion->prepare(
                'SELECT visiteur.iscomptable FROM visiteur '
                . 'WHERE visiteur.id = :unIdVisiteur '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $estcomptable = $requetePrepare->fetch();
        return $estcomptable[0];
    }

    /**
     * Retourne la liste des visiteurs
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getVisiteurs(): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT visiteur.id AS id, visiteur.nom AS nom, '
                . 'visiteur.prenom AS prenom '
                . 'FROM visiteur '
                . 'Where iscomptable = False'
        );
        $requetePrepare->execute();
        $response = $requetePrepare->fetchAll();
        return $response;
    }

    /**
     * Retourne la liste des fiches frais a valider
     * @return uun tableau assossiatif des fiches de frais à valider
     */
    public function getFichesFraisAMP(): array|bool {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.idvisiteur AS idvisiteur, fichefrais.mois AS mois, '
                . 'fichefrais.nbjustificatifs AS nbjusti, '
                . 'fichefrais.montantvalide AS montantvalide, '
                . 'fichefrais.datemodif AS datemodif '
                . 'FROM fichefrais '
                . 'WHERE idetat = "VA"'
        );
        $requetePrepare->execute();
        $lesFiches = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numMois = substr($mois, 4, 2);
            $lesFiches[] = array(
                'idvisiteur' => $laLigne['idvisiteur'],
                'mois' => $numMois,
                'nbjustificatifs' => $laLigne['nbjusti'],
                'montantvalide' => $laLigne['montantvalide'],
                'datemodif' => $laLigne['datemodif']
            );
        }
        return $lesFiches;
    }

    /*
     * Retourne le montant valide de la fiche de frais associé à un visiteur et un mois
     * @return le montant
     */

    public function getMontantValide($idVisiteur, $mois): ?string {
        $requetePrepare = $this->connexion->prepare(
                'SELECT montantvalide '
                . 'FROM fichefrais '
                . 'Where mois = :mois AND '
                . 'idvisiteur = :idVisiteur AND '
                . 'idetat = "VA"'
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $response = $requetePrepare->fetch();
        if ($response) {
            return $response[0];
        } else {
            return null;
        }
    }

    /**
     * Rend la somme des montant non refusées des frais hors forfait
     * @param type $idVisiteur
     * @param type $mois
     * @return int|null
     */
    public function getSommeMontantFraisHorsForfait($idVisiteur, $mois): ?int {
        $requetePrepare = $this->connexion->prepare(
                'SELECT SUM(montant) '
                . 'FROM lignefraishorsforfait '
                . 'Where mois = :mois AND '
                . 'idvisiteur = :idVisiteur AND '
                . 'refuse = False'
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $response = $requetePrepare->fetch();
        if ($response) {
            return $response[0];
        } else {
            return null;
        }
    }

    /**
     * Retourne un array avec le nom et prenom du visiteur
     * @param type $idVisiteur
     * @return array
     */
    public function getNomVisiteur($idVisiteur): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT nom, prenom '
                . 'FROM visiteur '
                . 'Where id = :idVisiteur'
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $response = $requetePrepare->fetch();
        if ($response) {
            return $response;
        } else {
            return null;
        }
    }

    /**
     * Passe la ligne hors forfait en refusé
     * 
     * @param type $idVisiteur
     * @param type $mois
     */
    public function ligneFraisHorsForfaitRefuse($id) {
        $requetePrepare = $this->connexion->prepare(
                'UPDATE lignefraishorsforfait '
                . 'SET refuse = True '
                . 'WHERE id = :id'
        );
        $requetePrepare->bindParam(':id', $id, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Met à jour la table ligneFraisHorsForfait
     * Met à jour la table ligneFraisHorsForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     * 
     * @param type $idVisiteur
     * @param type $mois
     * @param type $date
     * @param type $libelle
     * @param type $montant
     * @param type $id
     */
    public function majFraisHF($date, $libelle, $montant, $id) {
        $requetePrepare = $this->connexion->prepare(
                'UPDATE lignefraishorsforfait '
                . 'SET lignefraishorsforfait.date = :date, '
                . 'lignefraishorsforfait.libelle = :libelle, '
                . 'lignefraishorsforfait.montant = :montant '
                . 'WHERE lignefraishorsforfait.id = :id'
        );

        $requetePrepare->bindParam(':date', $date, PDO::PARAM_STR);
        $requetePrepare->bindParam(':libelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':montant', $montant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':id', $id, PDO::PARAM_INT);
        $requetePrepare->execute();
    }
    
    /**
     * Creer un enregistrement dans la table generPDF permettant d'empecher de generer
     * plusieurs pdf pour le meme mois
     * 
     * @param type $mois
     * @param type $idVisiteur
     * @param type $name
     */
    public function createEnregistrementPDF($idVisiteur, $mois, $namePDF) {
        $requetePrepare = $this->connexion->prepare(
                'INSERT INTO generPDF(mois, idvisiteur, nomPDF) '
                . 'VALUES (:unMois,:unIdVisiteur, :namePDF)'
                );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':namePDF', $namePDF, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Verifie si un pdf a deja été generée
     * 
     * @param type $idVisiteur
     * @param type $mois
     * @return type
     */
    public function verifPDF($idVisiteur, $mois){
        $requetePrepare = $this->connexion->prepare(
                'SELECT Count(*)'
                . 'FROM generPDF'
                . ' Where idvisiteur = :idVisiteur'
                . ' AND mois = :unMois'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $response = $requetePrepare->fetch();
    }
    
    /**
     * Donne le montant Kilometrique en fonction du type de vehicule du 
     * visiteur
     * 
     * @param type $idVisiteur
     * @return type
     */
    public function getMontantKilometre($idVisiteur) {
        $requetePrepare = $this->connexion->prepare(
                'SELECT typeVehicule.tarifKm'
                . ' FROM typeVehicule'
                . ' INNER JOIN visiteur ON visiteur.idTypeVehicule = typeVehicule.id'
                . ' WHERE visiteur.id = :idVisiteur'
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $response = $requetePrepare->fetch();
        return $response[0];
    }
    
    /**
     * Passe la fiche frais à l'etat de validée.
     * 
     * @param type $idVisiteur
     * @param type $mois
     * @param type $montant
     */
    public function valideFicheFrais($idVisiteur, $mois, $montant) {
        $requetePrepare = $this->connexion->prepare(
                'UPDATE ficheFrais '
                . 'SET montantvalide = :montant, '
                . 'datemodif = now(), '
                . 'idetat = "VA" '
                . 'WHERE idVisiteur = :idVisiteur AND '
                . 'mois = :mois'
        );
        $requetePrepare->bindParam(':montant', $montant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
}
