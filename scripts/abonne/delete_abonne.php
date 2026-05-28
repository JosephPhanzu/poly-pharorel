<?php

use App\Abonne;

try {
    
    $id = isset( $_POST["id"] ) ? $_POST["id"] : "";
    // testDebug($id);
    if (Abonne::deleteById($id)) :
        messageServer('success', 'L\'élément supprimé avec succèss');
    endif;
    messageServer('error','Problème lors de la suppression');

} catch (\Throwable $th) {
    messageServer('error', $th->getMessage());
}