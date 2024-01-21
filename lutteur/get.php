<?php

use Taf\TafAuth;
use Taf\TableQuery;

try {
    require './config.php';
    require '../TableQuery.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    /* 
        $params
        contient tous les parametres envoyés par la methode POST
     */
    // toutes les actions nécéssitent une authentification
    $auth_reponse = $taf_auth->check_auth();
    if ($auth_reponse["status"] == false && count($params) == 0) {
        echo json_encode($auth_reponse);
        die;
    }

    $table_query = new TableQuery($table_name);

    // $condition=$table_query->dynamicCondition($params,"=");
    // $reponse["condition"]=$condition;
    // $query="select * from  $table_name ".$condition;
    if (isset($params['id'])) {


        $id = $params['id'];
        $query = "select * from  $table_name where id='" . $id . "'";
        // $query="select * from  ecurie where ecurie.id = ( select lutteur.id_ecurie from lutteur where lutteur.id ='".$id."')";
        $reponse["data"]["lutteur"] = $taf_config->get_db()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $reponse["data"]["ecurie"] = $taf_config->get_db()->query("select * from  ecurie where ecurie.id = ( select lutteur.id_ecurie from lutteur where lutteur.id ='" . $id . "')")->fetchAll(PDO::FETCH_ASSOC);

        // requete pour l'ecurie du lutteur portant l'id
        // $reponse["data"]["lutteur"] = $taf_config->get_db()->query("
        // SELECT nom_ecurie FROM ecurie JOIN lutteur on 
        // ")->fetchAll(PDO::FETCH_ASSOC);

        // requete pour le nombre de defaites du lutteur portant l'id
        $reponse["data"]["nombre_defaites"] = $taf_config->get_db()->query("
        SELECT COUNT(*) AS nombre_defaites
        FROM combat
        WHERE (id_lutteur1 = '" . $id . "' OR id_lutteur2 = '" . $id . "') AND resultat != '" . $id . "';
        // requete pour le nombre de victoires du lutteur portant l'id
        ")->fetchAll(PDO::FETCH_ASSOC);

        // requete pour le nombre de victoires du lutteur portant l'id
        $reponse["data"]["nombre_victoires"] = $taf_config->get_db()->query("
        SELECT COUNT(*) AS nombre_victoires
        FROM combat
        WHERE (id_lutteur1 = '" . $id . "' OR id_lutteur2 = '" . $id . "') AND resultat = '" . $id . "';
        ")->fetchAll(PDO::FETCH_ASSOC);

        // requete pour le nombre de combat du lutteur portant l'id
        $reponse["data"]["nombre_combats"] = $taf_config->get_db()->query("
        SELECT COUNT(*) AS nombre_combats
        FROM combat
        WHERE id_lutteur1 = '" . $id . "' OR id_lutteur2 = '" . $id . "';
        ")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        # code...
        $query = "select * from  $table_name";

        $reponse["data"]["lutteur"] = $taf_config->get_db()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    $reponse["status"] = true;

    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
