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

    // $reponse["data"]["les_admins"] = $taf_config->get_db()->query("select * from admin")->fetchAll(PDO::FETCH_ASSOC);
    // $reponse["data"]["les_ecuries"] = $taf_config->get_db()->query("select * from ecurie")->fetchAll(PDO::FETCH_ASSOC);
    // $reponse["data"]["les_ecuries_lutteurs"] = $taf_config->get_db()->query("select * from ecurie CROSS JOIN lutteur where lutteur.id_ecurie=ecurie.id")->fetchAll(PDO::FETCH_ASSOC);
    $reponse["data"]["nombreCombatLutteur"] = $taf_config->get_db()->query("
    SELECT lutteur.id, lutteur.nom,lutteur.photo, COUNT(*) AS nombre_combats
    FROM lutteur
    JOIN combat ON lutteur.id = combat.id_lutteur1 OR lutteur.id = combat.id_lutteur2
    GROUP BY lutteur.id, lutteur.nom,lutteur.photo;
")->fetchAll(PDO::FETCH_ASSOC);
    $reponse["data"]["nombreVictoireLutteur"] = $taf_config->get_db()->query("
    SELECT lutteur.id, lutteurs.nom, lutteur.photo,
    SUM(CASE WHEN combat.resultat = 1 THEN 1 ELSE 0 END) AS victoires
    FROM lutteur
    LEFT JOIN combat ON lutteur.id = combat.id_lutteur1 OR lutteur.id = combat.id_lutteur2
    GROUP BY lutteur.id, lutteur.nom,lutteur.photo;
")->fetchAll(PDO::FETCH_ASSOC);
    $reponse["data"]["nombreDefaiteLutteur"] = $taf_config->get_db()->query("
    SELECT lutteur.id, lutteurs.nom, lutteur.photo,
    SUM(CASE WHEN combat.resultat = 0 THEN 1 ELSE 0 END) AS victoires
    FROM lutteur
    LEFT JOIN combat ON lutteur.id = combat.id_lutteur1 OR lutteur.id = combat.id_lutteur2
    GROUP BY lutteur.id, lutteur.nom,lutteur.photo;
")->fetchAll(PDO::FETCH_ASSOC);


    $reponse["status"] = true;

    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
