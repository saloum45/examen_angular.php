<?php

use Taf\TafAuth;
use Taf\TableQuery;

try {
    require './config.php';
    require '../TableQuery.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    // toutes les actions nécéssitent une authentification
    $auth_reponse = $taf_auth->check_auth($reponse);
    if ($auth_reponse["status"] == false) {
        echo json_encode($auth_reponse);
        die;
    }

    $table_query = new TableQuery($table_name);
    /* 
        $params
        contient tous les parametres envoyés par la methode POST
     */

    // preparation de la reqête
    extract($params);
    if (isset($file)) {
        # code...
        
        $img_nom = $_FILES['file']['name'];
        $tmp_nom = $_FILES['file']['tmp_name'];
        $time = time();
        $nouveau_nom_img = $time . $img_nom;
        $deplacer_image = move_uploaded_file($tmp_nom, "upload/" . $nouveau_nom_img);

        $query = "UPDATE  `lutteur` SET  `nom`='$nom',`photo`='$nouveau_nom_img', `description`='$description',`taille`='$taille', `poids`='$poids',`age`='$age',`id_admin`='$id_admin',`id_ecurie`='$id_ecurie'WHERE id='$id'";
    }else{

        $query = "UPDATE  `lutteur` SET  `nom`='$nom',`photo`='$photo', `description`='$description',`taille`='$taille', `poids`='$poids',`age`='$age',`id_admin`='$id_admin',`id_ecurie`='$id_ecurie'WHERE id='$id'";
    }
    // fin retouche 

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
