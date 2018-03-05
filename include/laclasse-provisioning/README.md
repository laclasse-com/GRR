# Laclasse GRR 

Module de la classe pour GRR, ajoutant comme fonctionnalité :

### Aprovisionnement des comptes provenant de l'annuaire de laclasse.com,
 
Lors de la connexion via SSO, le compte utilisateur est créé selon son rôle : 
* Le statut global (à comprendre: rôle) de l'utilisateur dans GRR va dépendre de son type de profil dans laclasse.com :
    * super_admin : administrateur 
    * académie, tuteur ou élève : rôle par défaut défini dans GRR => visiteur ou utilisateur
    * le reste des profils : utilisateur
* En plus de ça, chaque utilisateur se verra ajouter en tant qu'administrateur de site si celui-ci à les droits suffisant ('DIR' ou 'ADM') sur le site. Un site dans GRR correspond à une structure dans laclasse.com.
* Le site qui sera affiché par défault dépendra du profil actif dans l'annuaire