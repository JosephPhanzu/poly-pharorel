<?php

    use App\Employe;
    use App\Proprietaire;
    
    require_once __DIR__ . '/../function.php';

    try {
        $email = securisation(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
        $mdp = securisation(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS));
        
        if (isset($email) && isset($mdp)) {
            if (!empty($email) && !empty($mdp)) {
                if (preg_match('/(@willpharma.com)$/i', $email)) :

                    new Employe();
                    new Proprietaire();

                    $utilisateur_info = Employe::login($email, $mdp) ;

                    if (!empty($utilisateur_info)) {
                        
                        $_SESSION['bi-pharma']['code'] = $utilisateur_info['code'];
                        $_SESSION['bi-pharma']['role'] = $utilisateur_info['role'];
                        $_SESSION['bi-pharma']['nom'] = $utilisateur_info['nom'];
                        $_SESSION['bi-pharma']['prenom'] = $utilisateur_info['prenom'];
                        $_SESSION['bi-pharma']['code_pharmacie'] = $utilisateur_info['code_pharmacie'];

                        $route = $_SESSION['bi-pharma']['role'] === "employe" ?  '/dashboard?a='.$_SESSION['bi-pharma']['code_pharmacie'] : '/home';

                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Connexion réussie!!!',
                            'route' => $route
                        ]);
                        exit;
                    } else {
                        
                        $utilisateur_info = Proprietaire::login($email, $mdp);
                        
                        if (!empty($utilisateur_info)) :
                            $_SESSION['bi-pharma']['code'] = $utilisateur_info['code'];
                            $_SESSION['bi-pharma']['role'] = "proprietaire";
                            $_SESSION['bi-pharma']['nom'] = $utilisateur_info['nom'];
                            $_SESSION['bi-pharma']['prenom'] = $utilisateur_info['prenom'];

                            echo json_encode([
                                'status' => 'success',
                                'message' => 'Connexion réussie!!!',
                                'route' => '/dashboard_proprietaire'
                            ]);
                            exit;
                        endif;
                        messageServer('error', 'Information incorrect');
                    }
                else :
                    messageServer('error', 'Format email incorrect');
                endif;
            }else{
                messageServer('error', 'Tous les champs sont obligatoires!');
            }
        }else{
            messageServer('error', 'Champs non existant');
        }
    } catch (\Throwable $th) {
        messageServer('error', $th->getMessage());
    }
