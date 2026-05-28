<?php
use App\Consommation;
use App\Abonne;
use App\Facture;
require_once __DIR__ . '/../function.php';

try {

    $anneeAndMois = isset($_POST['mois']) ? filter_input(INPUT_POST, securisation('mois'), FILTER_SANITIZE_SPECIAL_CHARS) : null;

    $mois = explode(' ', $anneeAndMois)[0] ?? null;
    $annee = explode(' ', $anneeAndMois)[1] ?? null;

    $id_selected = isset($_POST['abonneId']) ? filter_input(INPUT_POST, securisation('abonneId'), FILTER_SANITIZE_SPECIAL_CHARS) : null;

    $index_ancien = isset($_POST['ancien']) ? filter_input(INPUT_POST, securisation('ancien'), FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $index_nouveau = isset($_POST['nouveau']) ? filter_input(INPUT_POST, securisation('nouveau'), FILTER_SANITIZE_SPECIAL_CHARS) : null;


    if(!empty($id_selected)) {
        $abonne = new Abonne();
        $abonne_info = $abonne->findByParams('abonne', 'id = :id', ['id' => $id_selected]);
        if (empty($abonne_info)) {
            messageServer('error', 'Abonné non trouvé');
        }
        $code_abonne = $abonne_info['code'];
    } else {
        messageServer('error', 'Aucun abonné sélectionné');
    }

    // testDebug($index_ancien);
    
    if (!empty($code_abonne) && !empty($mois) && !empty($annee) && !empty($index_ancien) && !empty($index_nouveau)) :

        $conso_inst = new Consommation($code_abonne, $mois, $annee, $index_ancien, $index_nouveau);

        if ($conso_inst->exist()) :
            messageServer('error','La Consommation renseignée existe déjà');
        endif;
        
        if ($info_conso = $conso_inst->add()) :
            // Création de la facture correspondante
            $code_conso = $info_conso['code'];
            $consommation = $index_nouveau - $index_ancien;
            $montant = $consommation * 665; // Supposons que le tarif est de 665 FCFA par unité consommée
            
            //Ajout de la TVA de 3.5%
            $tva = $montant * 0.035;
            $montant_total = $montant + $tva;
            
            $facture = new Facture($code_conso, $montant_total, 'Non Payé', $anneeAndMois);

            if ($facture->add()) :
                messageServer('success','Consommation ajoutée avec succès!');
            endif;

            messageServer('error','Erreur lors de l\'insertion de la Facture');

        endif;

        messageServer('error','Erreur lors de l\'insertion de la Consommation');

    endif;

    messageServer('error','Veuillez renseiger tout le champs obligatoire');

} catch (\Throwable $th) {
    die('Erreur Serveur'. $th->getMessage());
}