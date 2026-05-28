<?php

namespace App;
class Medecin extends Database {

    protected static $table = "medecin";
    private static $nom, $prenom, $specialite, $telphone, $email, $mdp, $statut, $config;

    public function __construct($nom = null, $prenom = null, $specialite = null, $telephone = null, $email = null, $mdp = null, $statut = null) {

        self::$nom = $nom;
        self::$prenom = $prenom;
        self::$specialite = $specialite;
        self::$telphone = $telephone;
        self::$email = $email;
        self::$mdp = $mdp;
        self::$statut = $statut;
        self::$config = (ConfigDB::getInstance())->getConfig();

        parent::__construct(self::$config);

    }

    public static function add() {

        $data = [
            'nom' => self::$nom,
            'prenom' => self::$prenom,
            'specialitee' => self::$specialite,
            'telephone' => self::$telphone,
            'email' => self::$email,
            'mdp' => password_hash(password: self::$mdp, algo: PASSWORD_BCRYPT),
            'code' => bin2hex(random_bytes(16)),
            'statut' => self::$statut
        ];

        try {
            self::insert(self::$table, $data);
            $id = self::$db->lastInsertId();
            return self::findByParams(self::$table, 'id = :id', ['id' => $id]);
        } catch (\Exception $th) {
            die('Erreur lors de l\'insertion utilisateur'. $th->getMessage());
        }
    }

    public static function updateInfo($code_user, $nom, $prenom, $specialite, $email, $telephone, $statut) {

        $data = [
            $nom,
            $prenom,
            $specialite,
            $email,
            $telephone,
            $statut,
            $code_user
        ];
        try {
            return self::updateByParam(self::$table, 'nom = ?, prenom = ?, specialitee = ?, email = ?, telephone = ?, statut = ?', 'code = ?', $data);
        } catch (\Exception $th) {
            die($th->getMessage());
        }
    }

    public static function verifyPassword($code_user, $old_mdp, $table) {
        $user = self::find($table, $code_user);

        if ($user) {
            if (password_verify($old_mdp, $user['mdp'])) :
                return true;
            endif;
        }
        
        return false;
    }

    public static function updatePassword($code_user, $new_mdp, $table) {

        $data = [
            password_hash(password: $new_mdp, algo: PASSWORD_BCRYPT),
            $code_user
        ];
        try {
            return self::updateByParam($table, 'mdp = ?', 'code = ?', $data);
        } catch (\Exception $th) {
            die($th->getMessage());
        }
    }

    public static function getAll() {
        $stmt = self::$db->prepare("SELECT * FROM ".self::$table." ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function exist (){

        $params = 'email = :email';
        $data = [
            'email' => self::$email
        ];
        return self::findByParams(self::$table, $params, $data);
    }

    public static function getByCode($code){
        return self::find(self::$table, $code);
    }

    public static function getPaginate($limit, $offset) {
        return self::paginate(self::$table, $limit, $offset);
    }

    public static function getProprio_valide($limit, $offset) {
        try {

            $stmt = self::$db->prepare(query: "
                SELECT DISTINCT pr.*, p.peut_connecter, p.peut_vendre
                FROM ".self::$table." pr
                INNER JOIN permission p ON p.code_user = pr.code
                ORDER BY pr.id
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue("limit", $limit, \PDO::PARAM_INT);
            $stmt->bindValue("offset", $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $th) {
            die("Error lors de le recup par pharma" . $th->getMessage());
        }
    }
    public static function getPatientByMed($limit, $offset) {
        try {

            $stmt = self::$db->prepare(query: "
                SELECT DISTINCT m.nom, m.prenom, m.specialite, p.nom, p.prenom, p.sexe, p.age
                FROM patients p
                INNER JOIN medecin m ON m.code = p.assigned_medecin
                ORDER BY p.nom
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue("limit", $limit, \PDO::PARAM_INT);
            $stmt->bindValue("offset", $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $th) {
            die("Error lors de le recup par pharma" . $th->getMessage());
        }
    }

    public static function getBySpeciality($specialite) {
        self::$specialite = $specialite;
        return $user = self::findAllByParams(self::$table, 'specielite = :specialite', ['specialite' => self::$specialite]);
    }

    public static function login($email, $mdp) {

        $user = self::findByParams(self::$table, 'email = :email', ['email' => $email]);

        if ($user) {
            if (password_verify($mdp, $user['mdp'])) :
                return $user;
            endif;
        }
        
        return [];
    }

    public static function deleteOne($code) {
        try {
            return self::delete(self::$table, $code);
        } catch (\Throwable $th) {
            die('Erreur lors de la suppression'. $th->getMessage());
        }
    }

}
