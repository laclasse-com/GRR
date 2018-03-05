<?php

/**
 * Récupère les types de profiles dans l'annuaire, les associent à un statut
 * sur GRR et les ajoute dans la table de correspondance si ceux-ci
 * n'existe pas. 
 * 
 * Le cas super admin est aussi géré mais differemment car pas inclus dans 
 * la liste des profils de l'annuaire 
 *
 * @param [array] $cfg : la variable contenant la configuration
 * @return void
 */
function populateCorrespondanceStatus($cfg)
{
    $profiles_types = json_decode(
        interroger_annuaire_ENT(
            $cfg['laclasse_addressbook_api_profiles_types'],
            $cfg['laclasse_addressbook_app_id'],
            $cfg['laclasse_addressbook_api_key']
        )
    );

    // Ajout du cas super admin géré à part
    $super_admin = (object) [
        'id' => 'SUPER_ADM',
        'name' => 'Super Administrateur'
    ];    
    array_push($profiles_types,$super_admin);

    // Vérifie l'existance des corrrespondance de statut
    // Créations de celles-ci si cela n'existe pas
    foreach ($profiles_types as $profile) {
        $code_function = $profile->id;
        $exists = grr_sql_query1("SELECT COUNT(*) FROM " . TABLE_PREFIX . "_correspondance_statut where code_fonction='$code_function'");
        if ($exists == 0) {
            $libellefonction = protect_data_sql($profile->name);
            switch ($code_function) {
                case 'SUPER_ADM':
                    $statut_grr = 'administrateur';
                    break;
                case 'ADM':
                case 'DIR':
                case 'DOC':
                case 'ENS':
                case 'ETA':
                case 'EVS':
                    $statut_grr = 'utilisateur';
                    break;
                default:
                    // ACA | ELV | TUT
                    //TODO A voir ce que l'on fait avec ça
                    $sso = Settings::get("sso_statut");
                    if ($sso == "cas_visiteur") {
                        $statut_grr = "visiteur";
                    } else if ($sso == "cas_utilisateur") {
                        $statut_grr = "utilisateur";
                    }

                    break;
            }

            grr_sql_command("INSERT INTO " . TABLE_PREFIX . "_correspondance_statut(code_fonction,libelle_fonction,statut_grr) VALUES ('$code_function', '$libellefonction', '$statut_grr')");
        }
    }
}
