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
 * $user_default_style
 *
 * // Used by laclasse-provisioning
 * $laclasse_user_id = phpCAS::getAttribute('uid');
 */

namespace Laclasse {

    require_once('laclasse_api.inc.php');

    // TODO Make a nice config file
    $cfg = array();

    $cfg['laclasse_addressbook_api_user'] = 'http://nelson.erasme.lan/api/users/';
    $cfg['laclasse_addressbook_api_etab'] = 'http://nelson.erasme.lan/api/structures/';
    $cfg['laclasse_addressbook_api_profiles_types'] = 'http://nelson.erasme.lan/api/profiles_types/';
    $cfg['laclasse_addressbook_app_id'] = 'GRR';
    $cfg['laclasse_addressbook_api_key'] = 'GaAE6gbOJDywGSUsbI8Qs+FrC+omrBek';

    // Récupération de l'utilisateur pour récupérer 
    // - id
    // - super_admin
    // - profiles
    $user_data = json_decode(interroger_annuaire_ENT(
        $cfg['laclasse_addressbook_api_user'] . $laclasse_user_id,
        $cfg['laclasse_addressbook_app_id'],
        $cfg['laclasse_addressbook_api_key'], array()));

    require_once("provisioning/profiles_types.inc.php");
    populateCorrespondanceStatus($cfg);

    require_once("provisioning/structures.inc.php");
    populateSites($cfg, $laclasse_user_id);

    if ($user_data->super_admin) {
        $user_code_fonction = 'SUPER_ADM';
    }


    require_once("grr/grr_statut_utilisateur.inc.php");
    selectDefaultSite($user_data);
    populateUserAdminSite($user_data);

}