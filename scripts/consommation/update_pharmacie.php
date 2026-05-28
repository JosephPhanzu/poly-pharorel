<?php

use App\Pharmacie; 
require_once __DIR__ . '/../function.php';

try {

    $code_pharmacie = isset($_POST['code_pharmacie']) ? filter_input(INPUT_POST, securisation('code_pharmacie'), FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $nom = isset($_POST['nom']) ? filter_input(INPUT_POST, securisation('nom'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $type = isset($_POST['type']) ? filter_input(INPUT_POST, securisation('type'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $description = isset($_POST['description']) ? filter_input(INPUT_POST, securisation('description'), FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $adresse = isset($_POST['adresse']) ? filter_input(INPUT_POST, securisation('adresse'), FILTER_SANITIZE_SPECIAL_CHARS) : '';

    if (!empty($nom) && !empty($description) && !empty($adresse) && !empty($type)) :

        $pharmacie_inst = new Pharmacie();

        if (empty($pharmacie_inst->getByCode($code_pharmacie))) :
            messageServer('error', 'La pharmacie n\'existe pas!');
        endif;

        if ($pharmacie_inst->updateInfo($code_pharmacie, $nom, $adresse, $description, $type)) :
            messageServer('success', 'Pharmacie modifiée avec succès!');
        endif;

        messageServer('error', 'Problème lors de la  modification!');
        
    endif;

    messageServer('error', 'Tous les champs sont obligatoires!');

} catch (\Exception $th) {
    messageServer('error', $th->getMessage());
}