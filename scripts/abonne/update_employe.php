<?php

use App\Employeur;
use App\Employe;
require_once __DIR__ . '/../function.php';

try {

    $code_user = $session->getUserCode();
    $role = $session->getRole();

    $nom = isset($_POST['nom']) ? filter_input(INPUT_POST, securisation('nom'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $prenom = isset($_POST['prenom']) ? filter_input(INPUT_POST, securisation('prenom'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $telephone = isset($_POST['telephone']) ? filter_input(INPUT_POST, securisation('telephone'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $salaire = isset($_POST['salaire']) ? filter_input(INPUT_POST, securisation('salaire'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $code_employe = isset($_POST['code_employe']) ? filter_input(INPUT_POST, securisation('code_employe'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $statut = isset($_POST['statut']) ? filter_input(INPUT_POST, securisation('statut'), FILTER_SANITIZE_SPECIAL_CHARS) : '';

    if ($role !== 'Employeur' && !empty(Employeur::getByCode($code_user))) :
        messageServer('error', 'Accès refusé!');
    endif;

    if (!empty($nom) && !empty($telephone) && !empty($email) && !empty($salaire) && !empty($code_employe) ) :

        new Employe();

        if (empty( Employe::getByCode($code_employe))) :
            messageServer('error', 'L\'employeur n\'existe pas!');
        endif;

        if (Employe::updateInfo($code_employe, $nom, $prenom, $salaire, $telephone, $statut)) :
            messageServer('success', 'Informations modifiées avec succès!');
        endif;

        messageServer('error', 'Problème lors de la  modification!');
        
    endif;

    messageServer('error', 'Tous les champs sont obligatoires!');

} catch (\Exception $th) {
    messageServer('error', $th->getMessage());
}