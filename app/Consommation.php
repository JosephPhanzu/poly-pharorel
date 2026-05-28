<?php

namespace App;

class Consommation extends Database {
    protected $table = 'consommation';

    				
    private $code_abonne, $mois, $annee, $index_ancien, $index_nouveau;

    private static $config;

    public function __construct($code_abonne = null, $mois = null, $annee = null, $index_ancien = null, $index_nouveau = null)
    {
        $this->code_abonne = $code_abonne;
        $this->mois = $mois;
        $this->annee = $annee;
        $this->index_ancien = $index_ancien;
        $this->index_nouveau = $index_nouveau;

        self::$config = (ConfigDB::getInstance())->getConfig();
        parent::__construct(self::$config);
    }

    public function add() {
        $data = [
            'code_abonne' => $this->code_abonne,
            'mois' => $this->mois,
            'annee' => $this->annee,
            'index_ancien' => $this->index_ancien,
            'index_nouveau' => $this->index_nouveau,
            'code' => bin2hex(random_bytes(16))
        ];
        try {
            self::insert($this->table, $data);
            $id = self::$db->lastInsertId();
            return self::findByParams($this->table, 'id = :id', ['id'=> $id]);
        } catch (\Exception $th) {
            die('Erreur lors de l\'insertion'. $th->getMessage());
        }
    }

    public function updateInfo($code_abonne, $annee, $index_nouveau, $nom_tit_carte, $index_ancien) {

        $data = [
            $annee,
            $index_nouveau,
            $nom_tit_carte,
            $index_ancien,
            $code_abonne
        ];
        try {
            return self::updateByParam($this->table, 'annee = ?, index_nouveau = ?, nom_tit_carte = ?, index_ancien = ?', 'code_abonne = ?', $data);
        } catch (\Exception $th) {
            die($th->getMessage());
        }
    }

    public function sumCotisations($code_abonneur) {
        $stmt = self::$db->prepare("SELECT SUM(mois) AS total FROM $this->table WHERE code_abonneur = :code_abonneur");
        $stmt->bindValue('code_abonneur', $code_abonneur, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function totalCotPaye($code_abonneur) {
        $stmt = self::$db->prepare("
            SELECT e.code_abonneur, COUNT(*) AS total 
            FROM $this->table c
            JOIN employes e ON c.code_abonne = e.code
            WHERE e.code_abonneur = :code_abonneur
        ");
        $stmt->bindValue('code_abonneur', (string) $code_abonneur, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getPaginate($limit, $offset) {
        try {
            return self::paginate($this->table, $limit, $offset);
        } catch (\Exception $th) {
            die('Erreur lors de la pagination'. $th->getMessage());
        }
    }

    public function getAll() {
        return self::all($this->table);
    }

    public function getFactureJoinConsoJoinAbonne() {
        try {
            $stmt = self::$db->prepare(query: "SELECT f.*, c.mois, c.annee, a.nom FROM facture f JOIN consommation c ON f.code_conso = c.code JOIN abonne a ON c.code_abonne = a.code ORDER BY f.date_facture DESC");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $th) {
            die("Error lors de le recup de tous les factures" . $th->getMessage());
        }
    }

    public function getAllConsommationJoinAbonne() {
        try {
            $stmt = self::$db->prepare(query: "SELECT c.*, a.nom FROM $this->table c JOIN abonne a ON c.code_abonne = a.code ORDER BY c.annee DESC, c.mois DESC");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $th) {
            die("Error lors de le recup de tous les consommations" . $th->getMessage());
        }
    }

    public function exist() {
        try {
            return self::findByParams($this->table, 'code_abonne = :code_abonne AND annee = :annee AND mois = :mois', ['code_abonne' => $this->code_abonne, 'annee' => $this->annee, 'mois' => $this->mois]);
        } catch (\Throwable $th) {
            die('Erreur lors de existSymptome'. $th->getMessage());
        }
    }

    public function getByAbonne($code_abonne) {
        try {
            return self::findByParams($this->table, 'code_abonne = :code_abonne', ['code_abonne' => $code_abonne]);
        } catch (\Throwable $th) {
            die('Erreur lors de getByAbonne'. $th->getMessage());
        }
    }

    public function getByCode($code) {
        try {
            return self::findByParams($this->table, 'code = :code', ['code' => $code]);
        } catch (\Throwable $th) {
            die('Erreur lors de getByCode'. $th->getMessage());
        }
    }

 
}