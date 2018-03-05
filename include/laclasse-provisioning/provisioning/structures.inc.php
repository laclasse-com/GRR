<?php

/**
 * Récupère les structures d'un utilisateur dans l'annuaire,
 * et selon leur présence dans la base de GRR, va les créer
 * ou les mettre à jour
 *
 * @param [array] $cfg: la configurateur
 * @param [string] $laclasse_user_id : l'id de l'utilisateur 
 * venant de se connecter via SSO
 * @return void
 */
function populateSites($cfg, $laclasse_user_id)
{
    $user_structures = json_decode(interroger_annuaire_ENT(
        $cfg['laclasse_addressbook_api_etab'],
        $cfg['laclasse_addressbook_app_id'], $cfg['laclasse_addressbook_api_key'],
        array("profiles.user_id" => $laclasse_user_id)));

    foreach ($user_structures as $structure) {
        // Création de la requête
        $exists = grr_sql_query1("SELECT COUNT(*) FROM " . TABLE_PREFIX . "_site where sitecode='$structure->id'");
        if ($exists == 0) {
            $sql = "INSERT INTO " . TABLE_PREFIX . "_site
        SET sitecode='" . strtoupper(protect_data_sql($structure->id)) . "',";
        } else {
            $sql = "UPDATE " . TABLE_PREFIX . "_site SET ";
        }

        // Séparation des champs existant si nécessaire.
        $sitename = substr($structure->name, 0, 50);
        $adresse_ligne1 = empty($structure->address) ? '' : substr($structure->address, 0, 38);
        $adresse_ligne2 = empty($structure->address) ? '' : substr($structure->address, 38, 38);
        $adresse_ligne3 = empty($structure->address) ? '' : substr($structure->address, 76, 38);
        $zip_code = empty($structure->zip_code) ? '' : substr($structure->zip_code, 0, 5);
        $city = isset($structure->city) ? $structure->city : '';
        $pays = '';
        $phone = isset($structure->phone) ? $structure->phone : '';
        $fax = isset($structure->fax) ? $structure->fax : '';

        $sql .= "sitename='" . protect_data_sql($sitename) . "',
            adresse_ligne1='" . protect_data_sql($adresse_ligne1) . "',
            adresse_ligne2='" . protect_data_sql($adresse_ligne2) . "',
            adresse_ligne3='" . protect_data_sql($adresse_ligne3) . "',
            cp='" . protect_data_sql($zip_code) . "',
            ville='" . strtoupper(protect_data_sql($city)) . "',
            pays='" . strtoupper(protect_data_sql($pays)) . "',
            tel='" . protect_data_sql($phone) . "',
            fax='" . protect_data_sql($fax) . "'";

        if ($exists > 0) {
            $sql .= " WHERE sitecode='" . strtoupper(protect_data_sql($structure->id)) . "'";
        }

        if (grr_sql_command($sql) < 0) {
            fatal_error(0, '<p>' . grr_sql_error() . '</p>');
        }
        if ($exists == 0) {
            mysqli_insert_id($GLOBALS['db_c']);
        }
    }
}
