<?php

use App\Consommation;

try {

    $conso_inst = new Consommation();
    
    $Consommation = $conso_inst->getAllConsommationJoinAbonne();
    // testDebug($Consommation);
    if (empty($Consommation)) {
        messageServer("error", "Aucune consommation trouvé");
    }

    messageServer('success','Consommation trouvé avec succès', $Consommation);

} catch (\Throwable $th) {
    die('Erreur serveur à la recuperation de données'. $th->getMessage());
}