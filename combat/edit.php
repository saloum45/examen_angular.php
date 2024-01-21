<?php
use Taf\TafAuth;
use Taf\TableQuery;
try {
    require './config.php';
    require '../TableQuery.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    // toutes les actions nécéssitent une authentification
    $auth_reponse=$taf_auth->check_auth($reponse);
    if ($auth_reponse["status"] == false) {
        echo json_encode($auth_reponse);
        die;
    }
    
    $table_query=new TableQuery($table_name);
   /* 
        $params
        contient tous les parametres envoyés par la methode POST
     */
    extract($_POST);
    $query = "UPDATE  `combat` SET  `titre`='$titre',`description_combat`='$description', `date_combat`='$date_combat',`id_lutteur1`='$id_lutteur1',`id_lutteur2`='$id_lutteur2',`resultat`='$resultat' WHERE id='$id'";
    // $reponse["query"]=$query;
    if ($taf_config->get_db()->exec($query)) {
        $reponse["status"] = true;
        $params["id_$table_name"] = $taf_config->get_db()->lastInsertId();
        $reponse["data"] = $params;
    } else {
        $reponse["status"] = false;
        $reponse["erreur"] = "Erreur d'insertion à la base de ";
    }
    echo json_encode($reponse);
} catch (\Throwable $th) {
    
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}

?>