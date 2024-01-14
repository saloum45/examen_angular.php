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

    // requête pour obtenir le nombre de combat par lutteur
    $reponse["data"]["nombreCombatLutteur"] = $taf_config->get_db()->query("
    SELECT lutteur.id, lutteur.nom,lutteur.photo, COUNT(*) AS nombre_combats,ecurie.nom_ecurie
    FROM lutteur
    JOIN combat ON lutteur.id = combat.id_lutteur1 OR lutteur.id = combat.id_lutteur2
	JOIN ecurie ON lutteur.id_ecurie=ecurie.id
    GROUP BY lutteur.id, lutteur.nom,lutteur.photo,ecurie.nom_ecurie
    ORDER by nombre_combats DESC;
    ")->fetchAll(PDO::FETCH_ASSOC);

    // requête pour obtenir le nombre de victoire par lutteur
    $reponse["data"]["nombreVictoireLutteur"] = $taf_config->get_db()->query("
    SELECT lutteur.id, lutteur.nom,lutteur.photo, ecurie.nom_ecurie,
       COUNT(combat.id) AS nombre_victoires
    FROM lutteur
    LEFT JOIN combat ON lutteur.id = combat.resultat
    JOIN ecurie ON lutteur.id_ecurie=ecurie.id
    GROUP BY lutteur.id, lutteur.nom,lutteur.photo,ecurie.nom_ecurie
    HAVING nombre_victoires>0
    ORDER BY nombre_victoires DESC;
    ")->fetchAll(PDO::FETCH_ASSOC);

    // requête pour obtenir le nombre de defaites par lutteur
    $reponse["data"]["nombreDefaiteLutteur"] = $taf_config->get_db()->query("
    SELECT lutteur.id, lutteur.nom,ecurie.nom_ecurie,lutteur.photo,
       COUNT(DISTINCT combat.id) - COUNT(DISTINCT CASE WHEN combat.resultat = lutteur.id THEN combat.id END) AS nombre_defaites
    FROM lutteur
    LEFT JOIN combat ON lutteur.id = combat.id_lutteur1 OR lutteur.id = combat.id_lutteur2
    JOIN ecurie ON lutteur.id_ecurie=ecurie.id
    GROUP BY lutteur.id, lutteur.nom,ecurie.nom_ecurie,lutteur.photo
    HAVING nombre_defaites > 0
    ORDER BY nombre_defaites DESC
    ")->fetchAll(PDO::FETCH_ASSOC);


    $reponse["status"] = true;

    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
