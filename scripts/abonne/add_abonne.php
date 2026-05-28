<?php
    
    use App\Abonne;
    use App\Permission;
    
    require_once __DIR__ . '/../function.php';
    
    try {
        
        $nom = isset($_POST['nom']) ? securisation(filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $email = isset($_POST['email']) ? securisation(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $numero_compteur = isset($_POST['numero_compteur']) ? securisation(filter_input(INPUT_POST, 'numero_compteur', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $telephone = isset($_POST['telephone']) ? securisation(filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $mdp = isset($_POST['mdp']) ? securisation(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $adresse = isset($_POST['adresse']) ? securisation(filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_SPECIAL_CHARS)) : null;
        $statut = isset($_POST['statut']) ? securisation(filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_SPECIAL_CHARS)) : 'Actif';
        $commune = isset($_POST['commune']) ? securisation(filter_input(INPUT_POST, 'commune', FILTER_SANITIZE_SPECIAL_CHARS)) : 'Nzadi';       
        
        // testDebug($statut);
        
        if (!empty($nom) && !empty($email) && !empty($numero_compteur) && !empty($telephone) && !empty($mdp) && !empty($adresse) && !empty($statut)) {
            if (preg_match('/^[0-9]{10}$/', $telephone)) :
                
                $statut = $statut ?? 'Actif';
                new Abonne($nom, $email, $numero_compteur, $telephone, $mdp, $adresse, $statut, $commune);
                
                if (Abonne::exist()) :
                    messageServer('error', 'Cet abonné avec ce matricule existe déjà!');
                endif;
                
                if ($proprio = Abonne::add()) {
                    $code_user = $proprio['code'];
                    $permission = new Permission('Abonne',  $code_user, 1, 1);
                    if ($permission->add()) :
                        messageServer('success', 'Compte abonné créé avec success!');
                    endif;
                    messageServer('error', 'Problème lors de l\'enregistrement permission!');
                } else {
                    messageServer('error', 'Erreur lors de l\'enregistrement!');
                }
                
            else :
                messageServer('error', 'Le numéro de téléphone doit contenir de chiffre et ne dépasse pas 10 caractère!');
            endif;
        }else{
            messageServer('error', 'Veuillez remplir tous les champs obligatoires!');
        }
    } catch (\Throwable $th) {
        throw $th;
    }
