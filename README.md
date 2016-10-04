# CANEO

### Projet réalisé par [AnonySmith](http://antoinevincent.github.io) !

#### Site réalisé pour Orthodontic's/Neo3d

##### Pré-requis pour l'installation de l'application sur votre environnement de travail : 

* [Symfony](https://symfony.com/)
* [Composer](http://symfony.com/doc/current/cookbook/composer.html)
* [MySQL](https://www.mysql.fr/)

Attention, si vous ne possédez pas tous ces pré-requis, ne passez pas à l'étape suivante.

##### Installation de l'application :

1. **Clonez** le dépôt GitHub sur votre PC 
> Nota : N'oubliez pas de cloner le dépôt au bon endroit.  
```sh
--> /var/www/html pour Linux.  
--> ~/Sites/ pour Mac OSX.
```

2. **Modifiez les droits** du dossier, via le terminal afin d'être sur que vous ayez tous les droits dessus (777).
```sh
--> sudo chmod -R 777 orthodeal/
```

3. **Installez** les dossiers manquants de l'application ("Vendor" et "Bin") avec Composer, en éxécutant la commande :
```sh
--> composer update
```

4. **Executez** les commandes suivantes après avoir initialiser le parameter.yml :
```sh
--> sudo php app/console doctrine:database:create
--> sudo php app/console doctrine:schema:update --force
--> sudo php app/console assets:install --symlink
```

###### L'application est prête à l'emploi.