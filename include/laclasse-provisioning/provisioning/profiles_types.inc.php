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