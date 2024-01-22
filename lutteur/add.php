<?php

use Taf\TafAuth;
use Taf\TableQuery;

try {
    require './config.php';
    require '../TableQuery.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    // toutes les actions nécéssitent une authentification
    $auth_reponse = $taf_auth->check_auth();
    if ($auth_reponse["status"] == false) {
        echo json_encode($auth_reponse);
        die;
    }

    $table_query = new TableQuery($table_name);
    /* 
        $params
        contient tous les parametres envoyés par la methode POST
     */

    $img_nom=$_FILES['file']['name'];
    $tmp_nom=$_FILES['file']['tmp_name'];
    $time=time();
    $nouveau_nom_img=$time.$img_nom;
    $deplacer_image=move_uploaded_file($tmp_nom,"upload/".$nouveau_nom_img);
    // preparation de la reqête
    extract($_POST);
    $query="INSERT INTO `lutteur`( `nom`, `photo`, `description`,`taille`, `poids`,`age`, `id_admin`,`id_ecurie`) VALUES ('".$nom."','".$nouveau_nom_img."','".$description."','".$taille."','".$poids."','".$age."','".$id_admin."','".$id_ecurie."')";
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
