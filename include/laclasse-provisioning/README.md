# Laclasse GRR 

Module de la classe pour GRR, ajoutant comme fonctionnalité :

### Aprovisionnement des comptes provenant de l'annuaire de laclasse.com,
 
Lors de la connexion via SSO :
* le compte utilisateur est créé selon son rôle 
    * Le statut global (à comprendre: rôle) de l'utilisateur dans GRR va dépendre de son type de profil dans laclasse.com :
        * super_admin : administrateur 
        * académie, tuteur ou élève : pas d'acces à l'outil tel quel
        * personnel de direction ou administrateur d'établissement : gestionnaire
        * le reste des profils : utilisateur
    * En plus de ça, chaque utilisateur se verra ajouter en tant qu'administrateur de site si celui-ci à les droits suffisant ('DIR' ou 'ADM') sur le site. Un site dans GRR correspond à une structure dans laclasse.com.
    * Le site qui sera affiché par défault dépendra du profil actif dans l'annuaire
    * Les infos du comptes sont mise à jour à chaque connexion (Spécificité: le statut est mis à jour que si le nouveau statut est meilleur que celui existant)
* le(s) structures dans lesquelles l'utilisateur à des droits sont crées si elle n'existent pas


### Configuration 

* La configuration des paramètres de l'instance de GRR se fait dans le fichier include/laclasse-provisioning/tables-laclasse.sql.
Le fichier est executé à la configuration de GRR via l'installateur automatique. C'est dans ce fichier qu'il faut
* A l'installation, laisser le préfix des tables à grr, certaines requêtes ne prennant pas en compte la possibilité des patterns, cela rendrait le site instable 

### Modifications comparé à une instance de GRR normale

Toutes les fonctionnalités de GRR ont été légèrement modifié pour faire du multi-site de façon restrainte selon ses profils laclasse.com
* La connexion autre que par SSO a été désactivée
* Un site correspond à une structure sur laclasse.com
* Une nouvelle table [PREFIX]\_j_user\_site à été créée : celle-ci fait le lien entre un utilisateur (non administrateur) et les sites qu'il a la possibilité de voir. Cette table est mise à jour à chaque connexion de l'utilisateur. Un administateur à accès à tout les sites, ce n'est donc pas nécessaire 
* La visibilité des utilisateurs pour les gestionnaires utilisateurs et administrateurs de site a été limitée aux utilisateurs sur lesquels ils ont des droits (à comprendre dans le même site)
* La visibilité des domaines/ressources pour tous sauf les administrateurs a été limitée à ceux qu'ils sont sensé voir
* La modification du nom/prénom/email n'est plus possible (ni utile car ces infos sont mis à jour à chaque connexion)
* Un gestionnaire d'utilisateur peut créer des gestionnaires d'utilisateurs
* Suppression de fonctionnalité administrateur de site : 
    * Supprimer toutes les réservations avant/après une date donnée
    * Importer un fichier d'occupation de salles au format CSV ou provenant de UnDeuxTemps
* Utilise la librairie PHPCas : En environnement local, remplacer dans Client.php la fonction _getServerBaseURL() pour gérer CAS en HTTP
```php
    private function _getServerBaseURL()
    {
        // the URL is build only when needed
        if ( empty($this->_server['base_url']) ) {
            if ($this->_getServerPort() == 80) {
                $this->_server['base_url'] = 'http://' . $this->_getServerHostname();
            } else {
                $this->_server['base_url'] = 'https://' . $this->_getServerHostname();
            }
            if (($this->_getServerPort() != 443) && ($this->_getServerPort() != 80)) {
                $this->_server['base_url'] .= ':'. $this->_getServerPort();
            }
            $this->_server['base_url'] .= $this->_getServerURI();
        }
        return $this->_server['base_url'];
    }
```