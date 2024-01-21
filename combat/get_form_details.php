<?php

use Taf\TafAuth;

try {
    require './config.php';
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
    if (isset($params['id'])) {
        # code...
        $reponse["data"]["combat"] = $taf_config->get_db()->query("SELECT 
        c.id AS id_combat,
        c.date_combat AS date_combat,
        c.description_combat AS description_combat,
        c.titre AS titre,
        l1.nom AS nom_lutteur1,
        l1.photo AS photo_lutteur1,
        l2.nom AS nom_lutteur2,
        l2.photo AS photo_lutteur2
        FROM combat c
        JOIN lutteur l1 ON c.id_lutteur1 = l1.id
        JOIN lutteur l2 ON c.id_lutteur2 = l2.id
        WHERE c.id='".$params['id']."'
    ;")->fetchAll(PDO::FETCH_ASSOC);
    }else{

        $reponse["data"]["combat"] = $taf_config->get_db()->query("SELECT 
        c.id AS id_combat,
        c.date_combat AS date_combat,
        c.titre AS titre,
        l1.nom AS nom_lutteur1,
        l1.photo AS photo_lutteur1,
        l2.nom AS nom_lutteur2,
        l2.photo AS photo_lutteur2
        FROM combat c
        JOIN lutteur l1 ON c.id_lutteur1 = l1.id
        JOIN lutteur l2 ON c.id_lutteur2 = l2.id
        ;")->fetchAll(PDO::FETCH_ASSOC);
        $reponse["data"]["les_admins"] = $taf_config->get_db()->query("select * from admin")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $reponse["status"] = true;
    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
