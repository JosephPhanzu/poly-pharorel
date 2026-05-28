<?php

use App\Employe;

$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["message" => "Token manquant"]);
    header("location : /login");
    exit;
}

try {
        
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page -1) * $limit;
    
    if (!class_exists('App\Employe')) {
        messageServer('error', 'Class Employe not found');
    }

    new Employe();
    
    $total_user = count(Employe::getAll());

    $total_page = ceil($total_user / $limit);

    $user = Employe::getPaginate($limit, $offset);
    if (empty($user)) {
        messageServer('error', 'Aucun user trouvé');
    }

    // $token = str_replace('Bearer ', '', $headers['Authorization']);
    // $userData = verifyJWT($token);

    messageServer('success', 'user trouvé avec success', $user, $total_page);

} catch (\Throwable $th) {
    die('Erreur serveur à la recuperation de données'. $th->getMessage());
}