<?php
session_start();
require_once __DIR__ .'/vendor/autoload.php';

use App\AuthentSession;
$session = AuthentSession::getInstance();

function testDebug($entre): never {
    echo json_encode(['message' => $entre]);
    exit;
}

function messageServer($statut, $message, $data = null, $total = null, $session = null): never {
    echo json_encode(['status' => $statut, 'message' => $message, 'data' => $data, 'total'=> $total, 'session', $session]);
    exit;
}

$requestUri = $_SERVER['REQUEST_URI'];

// Supprime les paramètres de requête, si présents
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Table de routage (URI => fichier PHP à inclure)
$routesGetMethode = [

    // Route for views
    '/' => __DIR__ . '/views/login.html',
    '/login' => __DIR__ . '/views/login.html',
    '/register' => __DIR__ . '/views/register.html',
    '/dashboard' => __DIR__ . '/views/home/dashboard.php',
    '/abonnes'=> __DIR__ . '/views/abonne/abonne.php',
    '/consommation' => __DIR__ . '/views/conso/conso.php',
    '/hospitalisations' => __DIR__ . '/views/hospitalisation/hospitalisation.php',
    '/factures' => __DIR__ . '/views/facture/facture.php',
    '/parametres' => __DIR__ . '/views/parametres/parametres.php',
    '/patients' => __DIR__ . '/views/patients/patients.php',
    '/consult_patient' => __DIR__ . '/views/medecin/consultation_patient.php',
    '/laboratoire' => __DIR__ . '/views/labo/labo.php',
    '/gestion_labo' => __DIR__ . '/views/labo/gest_labo.php',
    '/comptabilite' => __DIR__ . '/views/compta/comptabilite.php',
    '/alertes' => __DIR__ . '/views/alertes/alertes.php',
    '/modifier_produit' => __Dir__ . '/views/produits/update_produit.php',
    '/rapport' => __DIR__ . '/views/compta/rapport.php',
    '/inventaire' => __DIR__ . '/views/compta/eventaire.php',

    '/dashboard_admin' => __DIR__ . '/views/home/dashboard_admin.php',
    '/gestion_partenaire' => __DIR__ . '/views/partenaire/partenaires.php',
    '/info_partenaire' => __DIR__ . '/views/partenaire/info_partenaire.php',
    '/convention' => __DIR__ . '/views/convention/convention.php',
    '/facture_conv' => __DIR__ . '/views/convention/factures.php',
    '/detail_inventaire'=> __DIR__ . '/views/compta/detail_inv.php',
    
    // Route for Datas
    '/deconnexion' => __DIR__ . '/scripts/deconnexion.php',
    '/get_conso' => __DIR__ . '/scripts/consommation/get_conso.php',
    '/get_abonnes' => __DIR__ . '/scripts/abonne/get_abonnes.php',
    '/get_facture' => __DIR__ . '/scripts/consommation/get_facture.php',
    '/get_stats'=> __DIR__ . '/scripts/GenFunc/get_stats.php',
    '/get_produit_invent'=> __DIR__ . '/scripts/produit/get_produit_invent.php',
    '/get_rapport_inv' => __DIR__ . '/scripts/inventaire/get_rapport_invet.php',
    '/recherche_produit'=> __DIR__ . '/scripts/produit/recherche.php',
    '/get_panier'=> __DIR__ . '/scripts/panier/get_panier.php',
    '/vider_panier'=> __DIR__ . '/scripts/panier/clean_panier.php',
    '/get_vente' => __DIR__ . '/scripts/facture/get_facture.php',
    '/get_daily_report' => __DIR__ . '/scripts/facture/get_daily_report.php',
    // '/get_facture'=> __DIR__ . '/scripts/facture/get_one_facture.php',
    '/get_liste_invent' => __DIR__ . '/scripts/inventaire/get_produit_inventaire.php',
    '/get_convention' => __DIR__ . '/scripts/convention/get_conv.php',
    '/get_facture_conv' => __DIR__ . '/scripts/facture/get_facture_conv.php',

    '/get_pharmacie_by_propio' => __DIR__ . '/scripts/pharmacie/getByCodeProprio.php',
    '/get_user' => __DIR__ . '/scripts/users/get_user.php',

    '/get_partenaire' => __DIR__ . '/scripts/partenaire/get_partenaire.php',
    '/get_detail_invent' => __DIR__ . '/scripts/inventaire/get_detail_inventaire.php',
    '/get_rapport'=> __DIR__ . '/scripts/inventaire/get_rapport.php',

    // daily report view
    '/daily_report' => __DIR__ . '/views/compta/daily_report.php',

];

$routesPostMethod = [

    '/login' => __DIR__ . '/scripts/users/login.php',
    '/add_abonne' => __DIR__ . '/scripts/abonne/add_abonne.php',
    '/add_conso' => __DIR__ . '/scripts/consommation/add_conso.php',
    '/add_employe'=> __DIR__ . '/scripts/employe/add_employe.php',
    '/add_paiement' => __DIR__ . '/scripts/employe/add_paiement.php',
    '/add_produit'=> __DIR__ . '/scripts/produit/add_produit.php',
    '/delete_abonne'=> __DIR__ . '/scripts/abonne/delete_abonne.php',
    '/soustraire_produit'=> __DIR__ . '/scripts/panier/soustraire.php',
    '/retirer_produit'=> __DIR__ . '/scripts/panier/del_panier.php',
    '/add_vente' => __DIR__ . '/scripts/facture/add_facture.php',
    '/add_marge' => __DIR__ . '/scripts/marge/add_marge.php',
    '/add_taux' => __DIR__ . '/scripts/taux/add_taux.php',
    '/update_marge' => __DIR__ . '/scripts/produit/update_marge.php',
    '/update_produit' => __DIR__ . '/scripts/produit/update_produit.php',
    '/update_qteProduit' => __DIR__ . '/scripts/produit/update_qteProduit.php',
    '/sauvegarde_inv' => __DIR__ . '/scripts/inventaire/sauvegader.php',
    '/update_qte_actuelle' => __DIR__ . '/scripts/inventaire/update_qte.php',
    '/update_pharmacie' => __DIR__ . '/scripts/pharmacie/update_pharmacie.php',
    '/update_user' => __DIR__ . '/scripts/users/update_user.php',
    '/update_password' => __DIR__ . '/scripts/users/update_mdp.php',
    '/toggle_permission' => __DIR__ . '/scripts/permission/update_permission.php',
    '/add_convention' => __DIR__ . '/scripts/convention/add_conv.php',
    '/demmarer_arreter_inv' => __DIR__ . '/scripts/inventaire/add_inventaire.php',

    '/delete'=> __DIR__ . '/scripts/GenFunc/delete.php',
];

$routesPutMethod = [
    '/update_doctor' => __DIR__ . '/scripts/medecin/update_medecin.php',
];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if (array_key_exists($requestPath, $routesGetMethode)) {
        require $routesGetMethode[$requestPath];
    } else {
        http_response_code(404);
        echo "Page not found 404.";
    }   
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){

    if (array_key_exists($requestPath, $routesPostMethod)) {
        require $routesPostMethod[$requestPath];
    } else {
        http_response_code(404);
        echo "Scripts not found. Et non trouvé!";
    }
}elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'){

    if (array_key_exists($requestPath, $routesPutMethod)) {
        require $routesPutMethod[$requestPath];
    } else {
        http_response_code(404);
        echo "Scripts not found. Et non trouvé!";
    }
}else{
    echo "Méthode non autorisé";
}
