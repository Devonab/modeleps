#A propos

ContactForm est un module qui vous permet de réaliser un ou plusieurs formulaires personnalisables avec tout type de champs et le Recaptcha de Google.

##Installation

1.Tout d’abord, décompressez le fichier d’installation de ContactForm (contactfrm.zip), puis utilisez votre logiciel FTP pour copier le répertoire"contactfrm" ainsi obtenu dans le dossier "modules" de PrestaShop.
 
2. Une fois tous les fichiers uploadés, allez dans la partie administration de votre site et cliquez sur l’onglet "modules" depuis le menu du haut et choisissez "Autres modules" Ensuite "Installer", "Activer" et cliquez sur "Configurer".
 
3. Dans le tableau de bord de "ContactForm" vous devez en premier cliquer sur "Activer Contactfrm" avant toute chose, et choisir si vous souhaitez garder le formulaire d'origine de Prestashop en plus de ContactForm. Ou si vous souhaitez que ContactForm s'installe comme formulaire d'origine, de cette façon les liens "Contact" sont automatiquement mis à jour.
 
4. Ensuite cliquez sur "Configuration" pour effectuer les réglages de bases.
 
5. Cliquez ensuite "Ajouter des exemples de données" et cliquez sur le formulaire de votre choix et retournez dans l'accueil (bouton de gauche)
 
6. Il ne vous reste plus ensuite qu'à gérer votre formulaires en cliquant sur"Gérer vos formulaires", de là vous pouvez gérer vos champs et même dupliquer votre formulaire en un clic
 

### Recommandation

ATTENTION en cas de mise à jour : si vous avez une version de ContactForm inférieure à la 2.0 jusqu'à la 1.8.6, il vous faudra écraser les fichiers avec la nouvelle version 2.0 via votre FTP.
 
Ensuite, vous devez apliquer la requête suivante en remplaçant XX par le prefixe de votre base de données puis executer la requète dans phpMyadmin :
 
ALTER TABLE `XX_contactfrm` ADD `id_shop` INT NOT NULL DEFAULT '1'
 
Ceci evitera de devoir reconstruire vos formulaires.
 
En cas de problème de compatibilité, supprimer entièrement l'ancienne en la désintallant et refaites une installation propre. 

### Autres

Lors de l'installation du module ContactForm, il faut penser à vider votre cache Smarty, dans "Paramètres avancées" et "Performance", cliquer sur "Effacer le cache de Smarty" 

## Auteur

ARETMIC