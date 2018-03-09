<?php

namespace Laclasse;

/**
 * Correspondance entre statut laclasse.com et GRR
 *
 * @param string $code_function 
 * @return void
 */
function statut_grrFromLaclasseProfile($code_function) {
    switch ($code_function) {
        case 'administrateur':
            return $code_function;
            break;
        case 'ADM':
        case 'DIR':
            return 'gestionnaire_utilisateur';
            break;
        case 'DOC':
        case 'ENS':
        case 'ETA':
        case 'EVS':
            return 'utilisateur';
            break;
        default:
            // ACA | ELV | TUT : Aucune correspondance à faire
            // Ces utilisateurs n'auront pas accès à GRR dans un 1er temps
    }
}

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
            $statut_grr = statut_grrFromLaclasseProfile($code_function);
            if(isset($statut_grr)){
                grr_sql_command("INSERT INTO " . TABLE_PREFIX . "_correspondance_statut(code_fonction,libelle_fonction,statut_grr) VALUES ('$code_function', '$libellefonction', '$statut_grr')");
            }
        }
    }
}

/**
 * Récupère les droits les plus hauts selon les données utilisateurs 
 * provenant de l'annuaire laclasse.com
 *
 * @param mixed $profiles
 * @return string : administrateur si super_admin, le rôle le plus élevé dans le cas contraire
 */
function highestLaclasseProfile($user_data) {
    if ($user_data->super_admin) {
        return 'administrateur';
    }
    $role_order = [
        'ELV' => 0 ,
        'TUT' => 1 ,
        'ACA' => 2 ,
        'EVS' => 3 ,
        'DOC' => 4 ,
        'ENS' => 5 ,
        'ETA' => 6 ,
        'DIR' => 7 ,
        'ADM' => 8 
    ];
    $highest_role = 0; // If no profile we consider it as a student just so it doesn't crash GRR later
    foreach ($user_data->profiles as $profile) {
        $highest_role = max($highest_role, $role_order[$profile->type]);
    }

    return reset(array_keys($role_order,$highest_role));
}