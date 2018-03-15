<?php
/*
 * config_CAS.inc.php
 * Ce fichier permet de configurer la r�cup�ration dans GRR d'attributs LDAP des utilisateurs envoy�s par le serveur CAS Lire attentivement la documentation avant de modifier ce fichier
 * Derni�re modification : $Date: 2017-12-16 14:00$
 * @author    JeromeB & Laurent Delineau
 * @copyright Copyright 2003-2018 Team DEVOME - JeromeB
 * @link      http://www.gnu.org/licenses/licenses.html
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

/*
On ne peut pas invoquer directement la fonction "phpCAS::getAttribute()"
car elle n'est pas impl�ment�e dans "CAS/CAS.php"
Dans cette biblioth�que, il n'y a que "phpCAS::getAttributes()" qui soit d�finie, contrairement � ce qui se passe avec "CAS/CAS/client.php".
*/
function getAttribute($key)
{
	global $PHPCAS_CLIENT, $PHPCAS_AUTH_CHECK_CALL;
	if (!is_object($PHPCAS_CLIENT))
	{
		phpCAS :: error('this method should not be called before ' . __CLASS__ . '::client() or ' . __CLASS__ . '::proxy()');
	}
	if (!$PHPCAS_AUTH_CHECK_CALL['done'])
	{
		phpCAS :: error('this method should only be called after ' . __CLASS__ . '::forceAuthentication() or ' . __CLASS__ . '::isAuthenticated()');
	}
	if (!$PHPCAS_AUTH_CHECK_CALL['result'])
	{
		phpCAS :: error('authentication was checked (by ' . $PHPCAS_AUTH_CHECK_CALL['method'] . '() at ' . $PHPCAS_AUTH_CHECK_CALL['file'] . ':' . $PHPCAS_AUTH_CHECK_CALL['line'] . ') but the method returned FALSE');
	}
	return $PHPCAS_CLIENT->getAttribute($key);
}

 $user_nom = phpCAS::getAttribute('LaclasseNom');
 $user_prenom = phpCAS::getAttribute('LaclassePrenom');
 $user_language = '';
 $user_code_fonction = ''; // Non traité ici
 $user_libelle_fonction = ''; // Non traité ici
 $user_mail = phpCAS::getAttribute('MailAdressePrincipal');
 $user_default_style = '';

 // Used by laclasse-provisioning 
 // Override the default phpCAS behavior
 $login = phpCAS::getAttribute('uid');
 
?>
