<?php

// testDebug('recup facture');

use App\Consommation;

try {
    
    $conso_inst = new Consommation();
    
    $Consommation = $conso_inst->getFactureJoinConsoJoinAbonne();
    
    if (empty($Consommation)) {
        messageServer("error", "Aucune facture trouvé");
    }

    messageServer('success','Facture trouvé avec succès', $Consommation);

} catch (\Throwable $th) {
    die('Erreur serveur à la recuperation de données'. $th->getMessage());
}