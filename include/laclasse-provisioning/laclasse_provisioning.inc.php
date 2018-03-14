<?php
/**
* provisioning_laclasse.inc.php
* script de provisionning des comptes/sites/rôles et autres éléments
* provenant de laclasse.com
* Ce script fait partie de l'application GRR
* Dernière modification : $Date: 2017-12-16 14:00$
* @author    Nelson Goncalves <ngoncalves@erasme.org>
* @copyright Copyright 2008-2008 Laurent Delineau
* @author    JeromeB & Laurent Delineau & Olivier MOUNIER
* @author    Laurent Delineau
* @copyright Copyright 2003-2018 Team DEVOME - JeromeB
* @author    Yan Naessens
* @copyright Copyright 2017 Yan Naessens
* @link      http://www.gnu.org/licenses/licenses.html
*
* This file is part of GRR.
*
* GRR is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*/

/**
* Variables disponibles
*
* $user_nom
* $user_prenom
* $user_language
* $user_code_fonction
* $user_libelle_fonction
* $user_mail
* $login = phpCAS::getAttribute('uid');
*/

namespace Laclasse;

use \Settings;

require_once('laclasse_api.inc.php');
require_once("provisioning/profiles_types.inc.php");
require_once("provisioning/structures.inc.php");
require_once("grr/account.inc.php");

// Get other useful data that the CAS Response doesn't send
$user_data = json_decode(interroger_annuaire_ENT(
    Settings::get('laclasse_api_user') . $login,
    Settings::get('laclasse_app_id'),
    Settings::get('laclasse_api_key')));

$user_default_site = get_user_default_site($user_data);    
// Checks who is trying to login, no need to provide anything if he's forbidden
$user_code_fonction = highestLaclasseProfile($user_data);
$user_statut = statut_grrFromLaclasseProfile($user_code_fonction);
if(!isset($user_statut)) {
    // Show error screen or move that further down
    // TODO Create an error page   
    $redirect_url = url_origin($_SERVER);
    include "forbidden.php";
    die;
}


// Create object that contains user info
if(!isset($user_default_site)) {
    $user_default_site = 0;
}
$cas_tab_login["user_default_site"] = $user_default_site;
if (!isset($user_nom))
	$user_nom='';
$cas_tab_login["user_nom"] = $user_nom;
if (!isset($user_prenom))
	$user_prenom='';
$cas_tab_login["user_prenom"] = $user_prenom;
if (!isset($user_mail))
	$user_mail='';
$cas_tab_login["user_email"] = $user_mail;
if (!isset($user_statut))
	$user_statut='';
$cas_tab_login["user_statut"] = $user_statut;
if (!isset($user_libelle_fonction))
	$user_libelle_fonction='';
$cas_tab_login["user_libelle_fonction"] = $user_libelle_fonction;
if (!isset($user_language))
	$user_language='';
$cas_tab_login["user_language"] = $user_language;
if (!isset($user_default_style))
	$user_default_style='';
$cas_tab_login["user_default_style"] = $user_default_style;


populateSites($login);
// Create or update the account
handle_laclasse_sso_login($login, $cas_tab_login);
populate_user_admin_site($user_data);  
populate_user_site($user_data);