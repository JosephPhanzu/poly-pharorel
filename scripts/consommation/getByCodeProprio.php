<?php

use App\Pharmacie;

try {

    $code_proprio = isset($_GET['code_proprio']) ? $_GET['code_proprio'] : null;

    $pharmacie_inst = new Pharmacie();

    $pharmacie = $pharmacie_inst->findOne($code_proprio);

    if (empty($pharmacie)) :
        messageServer('error', 'Aucun pharmacie trouvé');
    endif;

    $role_session = $session->getRole();

    messageServer('success', 'pharmacie trouvé avec success', $pharmacie, $role_session);

} catch (\Throwable $th) {
    die('Erreur serveur à la recuperation de données'. $th->getMessage());
}