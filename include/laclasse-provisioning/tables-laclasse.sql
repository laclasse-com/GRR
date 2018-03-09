------------------------------------------
-- Suppression du compte administrateur --
------------------------------------------

TRUNCATE TABLE grr_utilisateurs;

---------------------------------------------------
-- Table d'association entre utilisateur et site --
---------------------------------------------------

DROP TABLE IF EXISTS grr_j_user_site;
CREATE TABLE grr_j_user_site (login varchar(40) NOT NULL default '', id_site int(11) NOT NULL default '0', PRIMARY KEY  (login,id_site) );


------------------------------------------------------
-- Spécifique à l'instance de GRR                   --
------------------------------------------------------

-- Contenu & Apparence

INSERT INTO grr_setting VALUES ('title_home_page', "Gestion et Réservation de Ressources") ON DUPLICATE KEY UPDATE VALUE = "Gestion et Réservation de Ressources";
INSERT INTO grr_setting VALUES ('message_home_page', "En raison du caractère personnel du contenu, ce site est soumis à des restrictions utilisateurs. Pour accéder aux outils de réservation, identifiez-vous :			") ON DUPLICATE KEY UPDATE VALUE = "En raison du caractère personnel du contenu, ce site est soumis à des restrictions utilisateurs. Pour accéder aux outils de réservation, identifiez-vous :			";
INSERT INTO grr_setting VALUES ('grr_url', 'http://nelson.erasme.lan/grr') ON DUPLICATE KEY UPDATE VALUE = 'http://nelson.erasme.lan/grr';
INSERT INTO grr_setting VALUES ('webmaster_name', 'Webmestre de GRR') ON DUPLICATE KEY UPDATE VALUE = 'Webmestre de GRR';
INSERT INTO grr_setting VALUES ('webmaster_email', 'admin@laclasse.local') ON DUPLICATE KEY UPDATE VALUE = 'admin@laclasse.local';
INSERT INTO grr_setting VALUES ('technical_support_email', 'support.technique@laclasse.local') ON DUPLICATE KEY UPDATE VALUE = 'support.technique@laclasse.local';
INSERT INTO grr_setting VALUES ('message_accueil', 'Bienvenue') ON DUPLICATE KEY UPDATE VALUE = 'Bienvenue';
INSERT INTO grr_setting VALUES ('company', 'Laclasse.com GRR') ON DUPLICATE KEY UPDATE VALUE = 'Laclasse.com GRR';

INSERT INTO grr_setting VALUES ('begin_bookings', '1504216800') ON DUPLICATE KEY UPDATE VALUE = '1504216800';
INSERT INTO grr_setting VALUES ('end_bookings', '1535752800') ON DUPLICATE KEY UPDATE VALUE = '1535752800'; -- A voir la date de fin des réservations

-- Accès et droits

-- Interactivité

INSERT INTO grr_setting VALUES ('grr_mail_from', 'noreply@laclasse.local') ON DUPLICATE KEY UPDATE VALUE = 'noreply@laclasse.local';
INSERT INTO grr_setting VALUES ('grr_mail_fromname', 'GRR Laclasse.com local') ON DUPLICATE KEY UPDATE VALUE = 'GRR Laclasse.com local';
INSERT INTO grr_setting VALUES ('grr_mail_Bcc', 'y') ON DUPLICATE KEY UPDATE VALUE = 'y';

-- Sécurité & Connexions

INSERT INTO grr_setting VALUES ('url_disconnect', 'http://nelson.erasme.lan/') ON DUPLICATE KEY UPDATE VALUE = 'http://nelson.erasme.lan/';

-- Activation de modules

-- Utilisateurs 

-- Configuration SSO

INSERT INTO grr_setting VALUES ('cas_racine', '/sso') ON DUPLICATE KEY UPDATE VALUE = '/sso';
INSERT INTO grr_setting VALUES ('cas_serveur', 'nelson.erasme.lan') ON DUPLICATE KEY UPDATE VALUE = 'nelson.erasme.lan';
INSERT INTO grr_setting VALUES ('cas_port', '80') ON DUPLICATE KEY UPDATE VALUE = '80';
INSERT INTO grr_setting VALUES ('cas_proxy_server', '') ON DUPLICATE KEY UPDATE VALUE = '';
INSERT INTO grr_setting VALUES ('cas_proxy_port', '') ON DUPLICATE KEY UPDATE VALUE = '';

INSERT INTO grr_setting VALUES ('Url_cacher_page_login', 'http://nelson.erasme.lan/sso/login?ticket=false&service=http%3A%2F%2Fnelson.erasme.lan%2Fgrr%2F') ON DUPLICATE KEY UPDATE VALUE = 'http://nelson.erasme.lan/sso/login?ticket=false&service=http%3A%2F%2Fnelson.erasme.lan%2Fgrr%2F';
INSERT INTO grr_setting VALUES ('Url_portail_sso', 'http://nelson.erasme.lan/portail/') ON DUPLICATE KEY UPDATE VALUE = 'http://nelson.erasme.lan/portail/';

------------------------------------------------------
-- Réglage par défaut                               --
------------------------------------------------------

-- Contenu & Apparence
INSERT INTO grr_setting VALUES ('mail_destinataire', '') ON DUPLICATE KEY UPDATE VALUE = '';
INSERT INTO grr_setting VALUES ('mail_etat_destinataire', '0') ON DUPLICATE KEY UPDATE VALUE = '0';
INSERT INTO grr_setting VALUES ('display_level_email', '2') ON DUPLICATE KEY UPDATE VALUE = '2';

-- Accès et droits

INSERT INTO grr_setting VALUES ('authentification_obli', '1') ON DUPLICATE KEY UPDATE VALUE = '1';
INSERT INTO grr_setting VALUES ('visu_fiche_description', '2') ON DUPLICATE KEY UPDATE VALUE = '2';
INSERT INTO grr_setting VALUES ('acces_fiche_reservation', '2') ON DUPLICATE KEY UPDATE VALUE = '2';
INSERT INTO grr_setting VALUES ('allow_search_level', '6') ON DUPLICATE KEY UPDATE VALUE = '6';
INSERT INTO grr_setting VALUES ('allow_user_delete_after_begin', '0') ON DUPLICATE KEY UPDATE VALUE = '0';

-- Interactivité

INSERT INTO grr_setting VALUES ('automatic_mail', 'yes') ON DUPLICATE KEY UPDATE VALUE = 'yes';
INSERT INTO grr_setting VALUES ('envoyer_email_avec_formulaire', 'no') ON DUPLICATE KEY UPDATE VALUE = 'no';
INSERT INTO grr_setting VALUES ('grr_mail_method', 'mail') ON DUPLICATE KEY UPDATE VALUE = 'mail';

INSERT INTO grr_setting VALUES ('javascript_info_admin_disabled', '1') ON DUPLICATE KEY UPDATE VALUE = '1';
INSERT INTO grr_setting VALUES ('javascript_info_disabled', '1') ON DUPLICATE KEY UPDATE VALUE = '1';
INSERT INTO grr_setting VALUES ('verif_reservation_auto', '0') ON DUPLICATE KEY UPDATE VALUE = '0';

-- Sécurité & Connexions

INSERT INTO grr_setting VALUES ('motdepasse_backup', '') ON DUPLICATE KEY UPDATE VALUE = ''; 
INSERT INTO grr_setting VALUES ('ip_autorise', '') ON DUPLICATE KEY UPDATE VALUE = '';
INSERT INTO grr_setting VALUES ('sessionMaxLength', '30') ON DUPLICATE KEY UPDATE VALUE = '30'; -- A voir avec Daniel
INSERT INTO grr_setting VALUES ('pass_leng', '32') ON DUPLICATE KEY UPDATE VALUE = '32';

-- Activation de modules

INSERT INTO grr_setting VALUES ('module_multisite', 'Oui') ON DUPLICATE KEY UPDATE VALUE = 'Oui';

-- Utilisateurs 

INSERT INTO grr_setting VALUES ('allow_users_modify_email', '5') ON DUPLICATE KEY UPDATE VALUE = '5';
INSERT INTO grr_setting VALUES ('allow_users_modify_mdp', '5') ON DUPLICATE KEY UPDATE VALUE = '5';
INSERT INTO grr_setting VALUES ('allow_users_modify_profil', '5') ON DUPLICATE KEY UPDATE VALUE = '5';

-- Configuration SSO

INSERT INTO grr_setting VALUES ('sso_statut', 'cas_visiteur') ON DUPLICATE KEY UPDATE VALUE = 'cas_visiteur';
INSERT INTO grr_setting VALUES ('sso_redirection_accueil_grr', 'n') ON DUPLICATE KEY UPDATE VALUE = 'n';
INSERT INTO grr_setting VALUES ('sso_ac_corr_profil_statut', 'n') ON DUPLICATE KEY UPDATE VALUE = 'n';
INSERT INTO grr_setting VALUES ('cacher_lien_deconnecter', 'y') ON DUPLICATE KEY UPDATE VALUE = 'y';
INSERT INTO grr_setting VALUES ('sso_IsNotAllowedModify', 'y') ON DUPLICATE KEY UPDATE VALUE = 'y';


