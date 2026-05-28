<?php

use App\Employeur;
use App\Employe;
use App\Cotisation;
require_once __DIR__ . '/../function.php';

try {

    $code_user = $session->getUserCode();
    $role = $session->getRole();

    $numero_paiement = isset($_POST['numero_paiement']) ? filter_input(INPUT_POST, securisation('numero_paiement'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $nom_tit_carte = isset($_POST['nom_tit_carte']) ? filter_input(INPUT_POST, securisation('nom_tit_carte'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $date_paiement = date('Y-m-d');
    $code_employe = isset($_POST['code_employe']) ? filter_input(INPUT_POST, securisation('code_employe'), FILTER_SANITIZE_SPECIAL_CHARS) : '';


    if ($role !== 'Employeur' && empty(Employeur::getByCode($code_user))) :
        messageServer('error', 'Accès refusé!');
    endif;

    if (!empty($numero_paiement) && !empty($nom_tit_carte) && !empty($code_employe) ) :

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

        new Employe();
        testDebug('Employe instancié');
        if (empty( Employe::getByCode($code_employe))) :
            messageServer('error', 'L\'employeur n\'existe pas!');
        endif;

        $cotisation_inst = new Cotisation();

        if ($cotisation_inst->updateInfo($code_employe, $date_paiement, $numero_paiement, $nom_tit_carte, "Payé")) :
            messageServer('success', 'Informations modifiées avec succès!');
        endif;

        messageServer('error', 'Problème lors de la  modification!');
        
    endif;

    messageServer('error', 'Tous les champs sont obligatoires!');

} catch (\Exception $th) {
    messageServer('error', $th->getMessage());
}