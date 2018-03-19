<?php

/**
 * This file highjacks the GRR's account provisioning since we know our
 * GRR instance will be using our SSO, doing it this way allows for better
 * control over what is done to provide the accounts mainly :
 *  - Disable account creation for some users depending on their type
 *  - Update other data from user on SSO login besides name, 
 *    surname and email
 * 
 * This also allow to contain the changes from the original GRR source
 * into a single folder which is always for the best in case of updates
 */

namespace Laclasse;


function create_account_if_not_exists($_login, $account_data) {
    // L'utilisateur n'est pas présent dans la base locale ou est inactif
		//  ou possède un mot de passe (utilisateur local GRR)
		// On teste si un utilisateur porte déjà le même login
		$test = grr_sql_query1("SELECT login FROM ".TABLE_PREFIX."_utilisateurs WHERE login = '".protect_data_sql($_login)."'");
		if ($test != '-1')
			return "3";
		$nom_user = $account_data["user_nom"];
		$email_user = $account_data["user_email"];
		$prenom_user = $account_data["user_prenom"];
		$code_fonction_user = $account_data["user_code_fonction"];
		$libelle_fonction_user = $account_data["user_libelle_fonction"];
		$language_user = $account_data["user_language"];
		$default_style_user = $account_data["user_default_style"];
		$statut_user = $account_data["user_statut"];
		$default_site_user = $account_data['user_default_site'];

		// On insère le nouvel utilisateur
		$sql = "INSERT INTO ".TABLE_PREFIX."_utilisateurs SET
		        nom='".protect_data_sql($nom_user)."',
				prenom='".protect_data_sql($prenom_user)."',
				login='".protect_data_sql($_login)."',
				password='',
				statut='".$statut_user."',
				email='".protect_data_sql($email_user)."',
				etat='actif',
				default_site='$default_site_user',";
		if (isset($default_style_user) and ($default_style_user!=""))
			$sql .= "default_style='".$default_style_user."',";
		if (isset($language_user) and ($language_user!=""))
			$sql .= "default_language='".$language_user."',";
		$sql .= "source='ext'";
		if (grr_sql_command($sql) < 0) {
            fatal_error(0, get_vocab("msg_login_created_error") . grr_sql_error());
		}
}

/**
 * 
 *
 * @param string $_login
 * @param array $new_account_data
 * @param array $old_account_data
 * @return void
 */
function update_existing_account($_login, $new_account_data, $old_account_data) {
    $nom_user = $new_account_data["user_nom"];
	$email_user = $new_account_data["user_email"];
	$prenom_user = $new_account_data["user_prenom"];
	$default_site_user = $new_account_data["user_default_site"];
	$statut_user = $new_account_data["user_statut"];
	
	$nom_en_base = $old_account_data[0];
	$prenom_en_base = $old_account_data[1];
	$email_en_base = $old_account_data[2];
	$statut_en_base = $old_account_data[3];
    $default_site_en_base = $old_account_data[4];
				  
	// Checks if user has a better statut in GRR
	// in this case we don't update it
    // visiteur < utilisateur < gestionnaire < administrateur 
	if(strcasecmp($statut_en_base, $statut_user) < 0) {
		$statut_user = $statut_en_base;
	}

    if ((strcmp($nom_en_base, $nom_user) != 0) ||
        (strcmp($prenom_en_base, $prenom_user) != 0) ||
		(strcmp($email_en_base, $email_user) != 0) ||
		(strcmp($statut_en_base, $statut_user) != 0) ||
		(strcmp($default_site_en_base, $default_site_user) != 0) ) {

			// Si l'un des champs est différent, on met à jour les champs
			$sql = "UPDATE ".TABLE_PREFIX."_utilisateurs SET
	        		nom='".protect_data_sql($nom_user)."',
			        prenom='".protect_data_sql($prenom_user)."',
					email='".protect_data_sql($email_user)."',
					statut='".protect_data_sql($statut_user)."',
					default_site='".protect_data_sql($default_site_user)."'
					where login='".protect_data_sql($_login)."'";
			if (grr_sql_command($sql) < 0)
				fatal_error(0, get_vocab("msg_login_created_error") . grr_sql_error());
		}
}

/**
 * This function is called after a successful login and handles all account related functions after login
 *
 * @param [type] $new_account_data
 * @return void
 */
function handle_laclasse_sso_login($_login, $account_data) {
    $sql = "SELECT nom, prenom, email,statut,default_site
			from ".TABLE_PREFIX."_utilisateurs
		where login = '" . protect_data_sql($_login) . "' and password = '' and etat != 'inactif'";
	$res_user = grr_sql_query($sql);
	$num_row = grr_sql_count($res_user);
	if ($num_row == 1) {
		$old_account_data = mysqli_fetch_array($res_user);
		update_existing_account($_login, $account_data,$old_account_data);
	} else {
		create_account_if_not_exists($_login, $account_data);
	}
}



/**
 * Met à jour la table d'administration des sites pour l'utilisateur venant de
 * se connecter
 * 
 * Ne le fait seulement que pour les utilisateur non super-admin
 * (car eux peuvent déjà gérer tout les sites)
 *
 * @param mixed $user_data
 * @return void
 */
function populate_user_admin_site($user_data) {
	if ($user_data->super_admin) return;
	
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
            $filtered_useradmin_array = array_filter($useradmin_site_rows, function($element) use($profile) { 
                return $element->sitecode == $profile->structure_id; 
            });
            // Always returns last value, not happy about that but should be good enough
            $useradmin_site = reset($filtered_useradmin_array);

            if(isset($useradmin_site) && empty($useradmin_site->login)) {
                // Ajouter la ligne dans grr_j_useradmin_site
                $sql = "INSERT INTO ".TABLE_PREFIX."_j_useradmin_site
                        (login, id_site)
                        VALUES('$user_data->id',$useradmin_site->id)";
				grr_sql_command($sql);	
				$useradmin_site->login =  $user_data->id;
			}
        }
    }
}

/**
 * Met à jour la table des sites auquel l'utilisateur à accès à la 
 * connexion
 * 
 * Cette table permet de faire l'affichage condition des utilisateurs
 * selon les structures auquelles appartiennes les utilisateurs
 * 
 * Ne le fait seulement que pour les utilisateur non super-admin
 * (car eux pourront voir toute la base des utilisateurs)
 *
 * @param mixed $user_data
 * @return void
 */
function populate_user_site($user_data) {
	if ($user_data->super_admin) return;

	$sql = "SELECT id, sitecode,login
            FROM ".TABLE_PREFIX."_j_user_site user_site
            RIGHT JOIN ".TABLE_PREFIX."_site site
            ON site.id = user_site.id_site
            AND user_site.login = '$user_data->id'
			ORDER BY sitecode ASC";
    $resultat = grr_sql_query($sql);

    for ($enr = 0; ($row = grr_sql_row($resultat, $enr)); $enr++) {
        $user_site_rows[] = (object) [
            'id' => $row[0],
            'sitecode' => $row[1],
            'login' => $row[2]
        ];    
	}
    
    foreach ($user_data->profiles as $profile) {
        if($profile->type != 'TUT' && $profile->type != 'ACA' && $profile->type != 'ELV') {
            // La ligne a prendre en compte
            $filtered_usersite_array = array_filter($user_site_rows, function($element) use($profile) { 
                return $element->sitecode == $profile->structure_id; 
            });
            // Always returns last value, not happy about that but should be good enough
            $user_site = reset($filtered_usersite_array);

            if(isset($user_site) && empty($user_site->login)) {
				// Ajouter la ligne dans grr_j_user_site crée par Nelson
				// pour stocker les sites où l'utilisateur à accès
                $sql = "INSERT INTO ".TABLE_PREFIX."_j_user_site
                        (login, id_site)
                        VALUES('$user_data->id',$user_site->id)";
				grr_sql_command($sql);
				$user_site->login =  $user_data->id;
			}
        }
    }
}


/**
 * Récupère du site par défaut selon le profil actif
 *
 * @param mixed $user_data
 * @return integer l'identifiant du site par défaut
 */
function get_user_default_site($user_data) {
    $actif_profiles = array_filter($user_data->profiles, function($profile) {
        return $profile->active;
    });
    $profile = reset($actif_profiles);
    
    $sql = "SELECT id FROM " . TABLE_PREFIX . "_site where sitecode='$profile->structure_id'";
    return grr_sql_query1($sql);
}