<?php

use App\Abonne;

try {
        
    // $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    // $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    // $offset = ($page -1) * $limit;

    // $Abonne_inst = new Abonne();
    
    new Abonne();
    
    $abonne = Abonne::getAllAbonne();
    // testDebug($abonne);
    if (empty($abonne)) {
        messageServer("error", "Aucun abonné trouvé");
    }

    messageServer('success','Abonné trouvé avec succès', $abonne);

} catch (\Throwable $th) {
    die('Erreur serveur à la recuperation de données'. $th->getMessage());
}