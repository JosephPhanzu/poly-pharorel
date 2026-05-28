<?php

    use App\Employe;
    use App\Employeur;
    use App\Cotisation;
    
    require_once __DIR__ . '/../function.php';
    
    try {

        $code_user = $session->getUserCode();
        $role = $session->getRole();
        
        $code_employeur = isset($_POST['code_employeur']) ? securisation(filter_input(INPUT_POST, 'code_employeur', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $code_employe = isset($_POST['code_employe']) ? securisation(filter_input(INPUT_POST, 'code_employe', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        
        $date_paiement = date('Y-m-d');
        $cotisation_statut = 'Payé';
        $numero_paiement = isset($_POST['numero_paiement']) ? securisation(filter_input(INPUT_POST, 'numero_paiement', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $nom_tit_carte = isset($_POST['nom_tit_carte']) ? securisation(filter_input(INPUT_POST, 'nom_tit_carte', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $expiry = isset($_POST['expiry']) ? securisation(filter_input(INPUT_POST, 'expiry', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $cvv = isset($_POST['cvv']) ? securisation(filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_SPECIAL_CHARS)) : null;

        new Employeur();
            
        if ($role !== 'Employeur' &&  empty(Employeur::getByCode($code_employeur))) :
            messageServer('error', 'Non autorisé!');
            
        endif;
        
        if (!empty($numero_paiement) && !empty($nom_tit_carte) && !empty($expiry) && !empty($cvv)) {

            // Validation expiry en regex
            if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry)) :
                messageServer('error', 'Format de date d\'expiration invalide, respecter le format MM/AA!');
            endif;

            // Validation numero de carte en regex
            if (!preg_match('/^[0-9]{4} [0-9]{4} [0-9]{4} [0-9]{4}$/', $numero_paiement)) :
                messageServer('error', 'Format de numéro de carte invalide!');
            endif;

            // Validation CVV en regex
            if (!preg_match('/^[0-9]{3}$/', $cvv)) :
                messageServer('error', 'Format de CVV invalide!');
            endif;


            // $expiryParts = explode('/', $expiry);
            // $expiryMonth = isset($expiryParts[0]) ? (int)$expiryParts[0] : 0;
            // $expiryYear = isset($expiryParts[1]) ? (int)$expiryParts[1] : 0;
            // //Recuperer les deux dernier chiffre de l'année

            // if ($expiryMonth < 1 || $expiryMonth > 12) {
            //     messageServer('error', 'Date d\'expiration invalide!');
            // }
            
            if (empty(Employe::getByCode($code_employe))) :
                messageServer('error', 'Cet employé n\'existe pas!');
            endif;
            
            $cotisation_inst = new Cotisation();
            
            if ($cotisation_inst->updateInfo($code_employe, $date_paiement, $numero_paiement, $nom_tit_carte, $cotisation_statut)) :
                
                messageServer('success', 'Paiement effectué avec succès!');
                
            else :
                messageServer('error', 'Erreur lors de l\'enregistrement du paiement!');
            endif;
            
        }else{
            messageServer('error', 'Veuillez remplir tous les champs obligatoires!');
        }
    } catch (\Throwable $th) {
        throw $th;
    }
