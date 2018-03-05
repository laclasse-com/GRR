<?php

namespace Laclasse;

/**
 * Met à jour la table d'administration des sites pour l'utilisateur venant de
 * se connecter
 *
 * @param [type] $user_data
 * @return void
 */
function populateUserAdminSite($user_data) {
    // Récupérer les sites existant pour l'utilisateur connecté si login est
    // null cela veut dire que l'utilisateur n'est pas un admin du site

    $sql = "SELECT id, sitecode,login
            FROM ".TABLE_PREFIX."_j_useradmin_site admin_site
            RIGHT JOIN ".TABLE_PREFIX."_site site
            ON site.id = admin_site.id_site
            AND admin_site.login = '$user_data->id'
			ORDER BY sitecode ASC";
    $resultat = grr_sql_query($sql);

    for ($enr = 0; ($row = grr_sql_row($resultat, $enr)); $enr++) {
        $useradmin_site_rows[] = (object) [
            'id' => $row[0],
            'sitecode' => $row[1],
            'login' => $row[2]
        ];    
	}
    
    foreach ($user_data->profiles as $profile) {
        if($profile->type == 'ADM' || $profile->type == 'DIR') {
            // La ligne a prendre en compte
            $filtered_useradmin_array = array_filter($useradmin_site_rows, sitecode_equals_structure_id($profile));
            // Always returns last value, not happy about that but should be good enough
            $useradmin_site = array_reduce($filtered_useradmin_array,function($carry,$item){return $item;});

            if(isset($useradmin_site) && empty($useradmin_site->login)) {
                // Ajouter la ligne dans grr_j_useradmin_site
                $sql = "INSERT INTO ".TABLE_PREFIX."_j_useradmin_site
                        (login, id_site)
                        VALUES('$user_data->id',$useradmin_site->id)";
                grr_sql_command($sql);
            } else if(isset($useradmin_site) ){
                // Supprimer de la liste des elements
                $useradmin_site_rows = array_udiff($useradmin_site_rows, array($useradmin_site), 'equals_objects');
            }
        }
    }
    // Pour chaque élément encore présent dans la liste et dont le login
    // est non null, le supprimer
    $elements_to_delete = array_filter($useradmin_site_rows, function($row) {
        return $row->login != null;
    });
    foreach ($elements_to_delete as $element) {
        $sql = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_site
                WHERE login= '$element->login' AND id_site= '$element->id'";
        grr_sql_command($sql);
    }
}


/**
 * Changement du domaine par défaut selon le profil actif
 *
 * @param mixed $user_data
 * @return void
 */
function selectDefaultSite($user_data) {
    //default_site
    $actif_profiles = array_filter($user_data->profiles, function($profile) {
        return $profile->active;
    })[0];
    $sql = "SELECT id FROM " . TABLE_PREFIX . "_site where sitecode='$actif_profiles->structure_id'";
    $site_id = grr_sql_query1($sql);
    $sql = "UPDATE " . TABLE_PREFIX . "_utilisateurs SET default_site = '$site_id' WHERE login='$user_data->id'";
    grr_sql_command($sql);
}


/**
 * Undocumented function
 *
 * @param [object] $profile : {id:number,sitecode:string,login:string}
 * @return void a callback that returns true or false depending
 *              if sitecode equals structure_id  
 */
function sitecode_equals_structure_id($profile = null) {
    // The "use" here binds $profile to the function at declare time.
    // This means that whenever $profile appears inside the anonymous
    // function, it will have the value it had when the anonymous
    // function was declared.
    return function($element) use($profile) { 
        return $element->sitecode == $profile->structure_id; 
    };
}

/**
 * Don't use this to compare objects globally
 * That's only used to check the objects 
 * created a bit higher in this file
 *
 * @param [type] $objA
 * @param [type] $objB
 * @return void
 */
function equals_objects($objA, $objB) {
    if($objA->id != $objB->id) return -1;
    if($objA->sitecode != $objB->sitecode) return -1;
    if($objA->login != $objB->login) return -1;

    return 0;
}