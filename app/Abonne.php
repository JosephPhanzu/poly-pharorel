<?php

namespace App;
class Abonne extends Database {

    protected static $table = "abonne";		
    		
    private static $nom, $email, $telephone, $mdp, $numero_compteur, $statut, $adresse, $commune;
    private static $config;

    public function __construct($nom = null, $email = null, $numero_compteur = null, $telephone = null, $mdp = null, $adresse = null, $statut = null, $commune = null) {

        self::$nom = $nom;
        self::$email = $email;
        self::$numero_compteur = $numero_compteur;
        self::$telephone = $telephone;
        self::$mdp = $mdp;
        self::$statut = $statut;
        self::$adresse = $adresse;
        self::$commune = $commune;
        self::$config = (ConfigDB::getInstance())->getConfig();

        parent::__construct(self::$config);

    }

    public static function add() {

        $data = [
            'nom' => self::$nom,
            'email' => self::$email,
            'telephone' => self::$telephone,
            'mdp' => self::$mdp,
            'adresse' => self::$adresse,
            'commune' => self::$commune,
            'numero_compteur'=> self::$numero_compteur,
            'statut' => self::$statut,
            'code' => bin2hex(random_bytes(16)),
        ];

        try {
            self::insert(self::$table, $data);
            $id = self::$db->lastInsertId();
            return self::findByParams(self::$table, 'id = :id', ['id' => $id]);
        } catch (\Exception $th) {
            die('Erreur lors de l\'insertion utilisateur'. $th->getMessage());
        }
    }

    public static function updateInfo($code_employe, $nom, $email, $telephone, $mdp, $statut) {

         $data = [
            $nom,
            $email,
            $telephone,
            $mdp,
            $statut,
            $code_employe
        ];  
        try {
            return self::updateByParam(self::$table, 'nom = ?, email = ?, salaire = ?, telephone = ?, statut = ?', 'code = ?', $data);
        } catch (\Exception $th) {
            die($th->getMessage());
        }
    }

    public static function getAll() {
        return self::all(self::$table);
    }

    public static function getAllAbonne() {
        try {
            $stmt = self::$db->prepare(query: "SELECT * FROM ".self::$table." ORDER BY nom");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $th) {
            die("Error lors de le recup de tous les abonnés" . $th->getMessage());
        }
    }

    public static function exist (){

        $params = 'telephone = :telephone';
        $data = [
            'telephone' => self::$telephone
        ];
        return self::findByParams(self::$table, $params, $data);
    }

    public static function getByCode($code){
        return self::find(self::$table, $code);
    }

    public static function getPaginate($limit, $offset) {
        return self::paginate(self::$table, $limit, $offset);
    }

    public static function getByMedecin($code_receptioniste, $limit, $offset) {
        try {

            $stmt = self::$db->prepare(query: "
                SELECT e.code, e.nom, e.email, e.salaire, e.telephone, e.mdp, e.numero_compteur, e.statut, e.adresse, c.montant, c.date_paiement, c.statut AS statut_cotisation
                FROM ".self::$table." e
                INNER JOIN cotisations c ON e.code = c.code_employe
                WHERE e.code_receptioniste = :code_receptioniste
                ORDER BY e.nom
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue("code_receptioniste", $code_receptioniste, \PDO::PARAM_STR);
            $stmt->bindValue("limit", $limit, \PDO::PARAM_INT);
            $stmt->bindValue("offset", $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $th) {
            die("Error lors de le recup par employeur" . $th->getMessage());
        }
    }

    public static function getAllByEmployeur($code_receptioniste) {
        try {

            $stmt = self::$db->prepare(query: "
                SELECT e.statut, e.mdp
                FROM ".self::$table." e
                WHERE e.code_receptioniste = :code_receptioniste
                ORDER BY e.nom
            ");
            $stmt->bindValue("code_receptioniste", $code_receptioniste, \PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $th) {
            die("Error lors de le recup par pharma" . $th->getMessage());
        }
    }

    public static function deleteOne($code) {
        try {
            return self::delete(self::$table, $code);
        } catch (\Throwable $th) {
            die('Erreur lors de la suppression'. $th->getMessage());
        }
    }

    public static function deleteById($id) {
        try {
            $stmt = self::$db->prepare("DELETE FROM ".self::$table." WHERE id = :id");
            $stmt->bindValue('id', $id, \PDO::PARAM_INT);
            return $stmt->execute();

            // $abonne = $stmt->fetch(\PDO::FETCH_ASSOC);
            // if ($abonne) :
            //     return self::delete(self::$table, $abonne['code']);
            // endif;
        } catch (\Throwable $th) {
            die('Erreur lors de la suppression'. $th->getMessage());
        }
    }
}
