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

    // if(empty($params)){
    //     $reponse["status"] = false;
    //     $reponse["erreur"] = "Parameters required";
    //     echo json_encode($reponse);
    //     exit;
    // }
    // // condition sur la modification
    // $condition=$table_query->dynamicCondition(json_decode($params["condition"]),'=');
    // // execution de la requete de modification
    // $query=$table_query->dynamicUpdate(json_decode($params["data"]),$condition);
    // //$reponse["query"]=$query;
    extract($_POST);
    if (isset($imageNotUpdated)) {
        $query = "UPDATE  `actualite` SET  `titre`='$titre', `contenu`='$contenu' WHERE id='$id'";
        # code...    
    } else {
        $img_nom = $_FILES['file']['name'];
        $tmp_nom = $_FILES['file']['tmp_name'];
        $time = time();
        $nouveau_nom_img = $time . $img_nom;
        $deplacer_image = move_uploaded_file($tmp_nom, "upload/" . $nouveau_nom_img);
        $query = "UPDATE  `actualite` SET  `titre`='$titre', `contenu`='$contenu',`photo`='$nouveau_nom_img' WHERE id='$id'";
    }
    $resultat = $taf_config->get_db()->exec($query);
    if ($resultat) {
        $reponse["status"] = true;
    } else {
        $reponse["status"] = false;
        $reponse["erreur"] = "Erreur! ou pas de moification";
    }
    echo json_encode($reponse);
} catch (\Throwable $th) {

    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
