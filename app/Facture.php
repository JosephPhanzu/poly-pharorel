<?php
    
    namespace App;    

    class Facture extends Database{

        protected $table = "facture";

        			
        private $code_conso, $montant, $statut, $date_facture;

        private static $config;

        public function __construct($code_conso = null, $montant = null, $statut = null, $date_facture = null) {

            $this->code_conso = $code_conso;
            $this->montant = $montant;
            $this->statut = $statut;
            $this->date_facture = $date_facture;

            self::$config = (ConfigDB::getInstance())->getConfig();
            parent::__construct(self::$config);
        }

        public function getAll() {
            try {
                return self::all($this->table);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        public function findOne($code) {
            try {
                return self::find($this->table, $code);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        public function getById ($id) {
            try {
                return self::findByParams($this->table, 'id = :id', ['id' => $id]);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        public function add(){

            try {

                self::insert($this->table, [
                    'code_conso' => $this->code_conso,
                    'montant' => $this->montant,
                    'statut' => $this->statut,
                    'date_facture'=> $this->date_facture,
                    'code' => bin2hex(random_bytes(16)),
                ]);
        
                $id = self::$db->lastInsertId();
                return self::findByParams($this->table, 'id = :id', ['id'=> $id]);

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la création de la facture : " . $e->getMessage());
            }
        }

        public function getByCode($date_facturecie) {
            return self::findAllByParams($this->table, 'date_facturecie = :code', ['code' => $date_facturecie]);
        }
        
        // Single facture for employé
        public function getFactureByPharmaUser($date_facturecie, $statut, $code_facture): array{
            try {

                $stmt = self::$db->prepare("
                    SELECT DISTINCT f.id, f.code AS code_facture, c.nom_client, f.montant, f.temps, e.nom AS nom_vendeur, fp.nom_produit, fp.quantite, fp.prix
                    FROM $this->table f
                    INNER JOIN client c ON f.code_membre = c.contact_client
                    INNER JOIN facture_produit fp ON f.code = fp.code_facture
                    INNER JOIN employe e ON f.code_utilisateur = e.code
                    WHERE f.date_facturecie = :date_facturecie AND f.code_utilisateur = :statut AND f.code = :code_facture
                ");
                $stmt->bindValue("date_facturecie", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("statut", $statut, \PDO::PARAM_STR);
                $stmt->bindValue("code_facture", $code_facture, \PDO::PARAM_STR);
                $stmt->execute();

                return $stmt->rowCount() > 0 ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la récupération des factures : " . $e->getMessage());
            }
        }

        // Single facture for proprio
        public function getArticleFactureByPharma($date_facturecie, $code_facture): array {
            try {
                // 2️⃣ Articles de la facture
                $produitsStmt = self::$db->prepare("
                    SELECT 
                        nom_produit, quantite, prix, (quantite * prix) AS sous_montant
                    FROM facture_produit
                    WHERE code_facture = :code_facture
                ");
                $produitsStmt->execute(['code_facture' => $code_facture]);
                return $produitsStmt->fetchAll(\PDO::FETCH_ASSOC);

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la récupération des factures : " . $e->getMessage());
            }
        }


        public function getFactureByPharma($date_facturecie, $code_facture): array{
            try {

                $stmt = self::$db->prepare("
                    SELECT DISTINCT
                        f.id, 
                        f.code AS code_facture, 
                        c.nom_client, 
                        f.montant, 
                        f.temps, 
                        e.nom AS nom_vendeur,
                        fp.nom_produit, 
                        fp.quantite, 
                        fp.prix,
                        (fp.quantite * fp.prix) AS sous_montant
                    FROM $this->table f
                    INNER JOIN client c ON f.code_conso = c.contact_client
                    INNER JOIN facture_produit fp ON f.code = fp.code_facture
                    INNER JOIN employe e ON f.code_utilisateur = e.code
                    WHERE f.date_facturecie = :date_facturecie 
                    AND f.code = :code_facture;


                ");
                $stmt->bindValue("date_facturecie", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("code_facture", $code_facture, \PDO::PARAM_STR);
                $stmt->execute();

                return $stmt->rowCount() > 0 ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la récupération des factures : " . $e->getMessage());
            }
        }

        // All sales for employé
        public function getAllFacturesDetailsByPharmaUser($date_facturecie, $statut, $debutJour, $finJour, $limit, $offset): array{
            try {

                $stmt = self::$db->prepare("
                    SELECT DISTINCT f.id, f.code AS code_facture, c.date_facturecie, c.nom_client, f.montant, f.temps, e.nom AS nom_vendeur
                    FROM $this->table f
                    INNER JOIN client c ON f.code_conso = c.contact_client
                    INNER JOIN employe e ON f.code_utilisateur = e.code
                    WHERE f.date_facturecie = :date_facturecie AND f.code_utilisateur = :statut AND c.date_facturecie = :pharma_client AND f.temps BETWEEN :debutJour AND :finJour
                    ORDER BY f.id DESC 
                    LIMIT :limit OFFSET :offset
                ");
                $stmt->bindValue("date_facturecie", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("statut", $statut, \PDO::PARAM_STR);
                $stmt->bindValue("pharma_client", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("debutJour", $debutJour, \PDO::PARAM_STR);
                $stmt->bindValue("finJour", $finJour, \PDO::PARAM_STR);
                $stmt->bindValue('limit', (int)$limit, \PDO::PARAM_INT);
                $stmt->bindValue('offset', (int)$offset, \PDO::PARAM_INT);
                $stmt->execute();

                return $stmt->rowCount() > 0 ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la récupération des factures : " . $e->getMessage());
            }
        }

        // All sales for proprio
        public function getAllFacturesDetailsByPharma($date_facturecie, $debutJour, $finJour, $limit, $offset): array{
            try {

                $stmt = self::$db->prepare(query: "
                    SELECT DISTINCT f.id, f.code AS code_facture, f.date_facturecie, c.nom_client, f.montant, f.temps, e.nom AS nom_vendeur
                    FROM $this->table f
                    INNER JOIN client c ON f.code_conso = c.contact_client
                    INNER JOIN employe e ON f.code_utilisateur = e.code
                    WHERE f.date_facturecie = :date_facturecie AND c.date_facturecie = :pharma_client AND f.temps BETWEEN :debutJour AND :finJour
                    ORDER BY f.id DESC 
                    LIMIT :limit OFFSET :offset
                ");
                $stmt->bindValue("date_facturecie", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("pharma_client", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("debutJour", $debutJour, \PDO::PARAM_STR);
                $stmt->bindValue("finJour", $finJour, \PDO::PARAM_STR);
                $stmt->bindValue('limit', (int)$limit, \PDO::PARAM_INT);
                $stmt->bindValue('offset', (int)$offset, \PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->rowCount() > 0 ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la récupération des factures : " . $e->getMessage());
            }
        }


        public function getAllFacturesByPharmaUser($date_facturecie, $statut, $debutJour, $finJour): array{
            try {

                $stmt = self::$db->prepare("
                    SELECT DISTINCT f.id, f.code AS code_facture, f.date_facturecie, c.nom_client, f.montant, f.temps, e.nom AS nom_vendeur
                    FROM $this->table f
                    INNER JOIN client c ON f.code_conso = c.contact_client
                    INNER JOIN employe e ON f.code_utilisateur = e.code
                    WHERE f.date_facturecie = :date_facturecie AND f.code_utilisateur = :statut AND c.date_facturecie = :pharma_client AND f.temps BETWEEN :debutJour AND :finJour
                ");
                $stmt->bindValue("date_facturecie", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("statut", $statut, \PDO::PARAM_STR);
                $stmt->bindValue("pharma_client", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("debutJour", $debutJour, \PDO::PARAM_STR);
                $stmt->bindValue("finJour", $finJour, \PDO::PARAM_STR);
                $stmt->execute();

                return $stmt->rowCount() > 0 ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la récupération des factures : " . $e->getMessage());
            }
        }

        // All sales for proprio
        public function getAllFacturesByPharma($date_facturecie, $debutJour, $finJour): array{
            try {

                $stmt = self::$db->prepare(query: "
                    SELECT DISTINCT f.id, f.code AS code_facture, f.date_facturecie, c.nom_client, f.montant, f.temps, e.nom AS nom_vendeur
                    FROM $this->table f
                    INNER JOIN client c ON f.code_conso = c.contact_client
                    INNER JOIN employe e ON f.code_utilisateur = e.code
                    WHERE f.date_facturecie = :date_facturecie AND c.date_facturecie = :pharma_client AND f.temps BETWEEN :debutJour AND :finJour
                ");
                $stmt->bindValue("date_facturecie", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("pharma_client", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("debutJour", $debutJour, \PDO::PARAM_STR);
                $stmt->bindValue("finJour", $finJour, \PDO::PARAM_STR);
                $stmt->execute();
                
                return $stmt->rowCount() > 0 ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la récupération des factures : " . $e->getMessage());
            }
        }


        public function getProduitPlusVenduByPharma($date_facturecie, $debutJour, $finJour): array{
            try {

                $stmt = self::$db->prepare("
                    SELECT DISTINCT c.nom_client, f.montant, f.temps, e.nom AS nom_vendeur, fp.nom_produit, fp.quantite, fp.prix
                    FROM $this->table f
                    INNER JOIN client c ON f.code_conso = c.contact_client
                    INNER JOIN facture_produit fp ON f.code = fp.code_facture
                    INNER JOIN employe e ON f.code_utilisateur = e.code
                    WHERE f.date_facturecie = :date_facturecie AND f.temps BETWEEN :debutJour AND :finJour
                ");
                $stmt->bindValue("date_facturecie", $date_facturecie, \PDO::PARAM_STR);
                $stmt->bindValue("debutJour", $debutJour, \PDO::PARAM_STR);
                $stmt->bindValue("finJour", $finJour, \PDO::PARAM_STR);
                $stmt->execute();

                return $stmt->rowCount() > 0 ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la récupération des factures : " . $e->getMessage());
            }
        }

        // Rapport de ventes journalier
        public function getDailyReport($date = null, $date_facturecie = null): array {
            try {
                if (empty($date)) {
                    $debutJour = strtotime('today');
                } else {
                    $debutJour = strtotime($date);
                }
                $finJour = $debutJour + 86400 - 1;

                $params = [
                    'debut' => $debutJour,
                    'fin' => $finJour
                ];

                $pharmaFilter = '';
                if (!empty($date_facturecie)) {
                    $pharmaFilter = ' AND f.date_facturecie = :date_facturecie';
                    $params['date_facturecie'] = $date_facturecie;
                }

                // montant ventes et nombre de factures
                $stmt = self::$db->prepare("SELECT COUNT(DISTINCT f.id) AS invoices_count, COALESCE(SUM(fp.quantite * fp.prix),0) AS montant_sales FROM $this->table f JOIN facture_produit fp ON f.code = fp.code_facture WHERE f.temps BETWEEN :debut AND :fin" . $pharmaFilter);
                $stmt->execute($params);
                $summary = $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['invoices_count' => 0, 'montant_sales' => 0];

                // Top produits
                $queryTop = "SELECT fp.nom_produit AS name, SUM(fp.quantite) AS qty, COALESCE(SUM(fp.quantite * fp.prix),0) AS sales FROM facture_produit fp JOIN $this->table f ON f.code = fp.code_facture WHERE f.temps BETWEEN :debut AND :fin" . $pharmaFilter . " GROUP BY fp.nom_produit ORDER BY qty DESC LIMIT 10";
                $stmtTop = self::$db->prepare($queryTop);
                $stmtTop->execute($params);
                $topProducts = $stmtTop->rowCount() > 0 ? $stmtTop->fetchAll(\PDO::FETCH_ASSOC) : [];

                return [
                    'date' => date('Y-m-d', $debutJour),
                    'montant_sales' => (float)$summary['montant_sales'],
                    'invoices_count' => (int)$summary['invoices_count'],
                    'top_products' => $topProducts
                ];

            } catch (\Exception $e) {
                throw new \Exception("Erreur lors de la génération du rapport : " . $e->getMessage());
            }
        }


    }
