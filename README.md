# Qizuna Starter Kit

## A quoi sert ce starter kit ? 

Ce kit sert à deployer rapidement une nouvelle application. L'objectif de ce starter kit est d'etre configurable sur de multiple points pour pouvoir déployer une base d'application dans un délai rapide.

### Elements compris dans le starter kit
- [Symfony](https://github.com/symfony) 
- Security ( login / logout / utilisateurs )
- [Reset Password](https://github.com/SymfonyCasts/reset-password-bundle)
- [Administration](https://github.com/EasyCorp/EasyAdminBundle)
- Webpack Encore ( compilation des assets)
- [Tailwind CSS](https://github.com/tailwindlabs/tailwindcss)
- Outils qualité 
  - [PHPStan](https://github.com/phpstan/phpstan)
  - [PHP Insights](https://github.com/nunomaduro/phpinsights)
  - [PHP CS](https://github.com/squizlabs/PHP_CodeSniffer)
  - [PHP CPD](https://github.com/sebastianbergmann/phpcpd)
- Testing (PHPUnit)
- Makefile (by [YoanDev](https://github.com/yoanbernabeu/Symfony-And-Docker-Makefile-Taskfile))

### Pré-requis à l'utilisation
- Avoir une installation de PHP 8.1 configurée
- Avoir un environnement Docker fonctionnel
- Disposer de la commande `Make`

## Comment utiliser ce starter kit ?

```shell
composer create-project qizuna/starter-kit ./myNewProjectFolder
```
Laisser le projet s'installer puis 

```shell
make first-install
```
Votre navigateur devrait s'ouvrir et afficher l'écran de connexion de l'application 😀.
A vous de commencer votre travail maintenant !

## Les outils qualité (QA)

S'il est possible d'appeller les outils qualité directement via docker, il est également possible de les 
appeler via des commandes préparées dans le fichier `Makefile`

### Les vérifications avec `before-commit`
La commande la plus utile sera certainement `make before-commit`. Cette commande lancera plusieurs outils QA les uns après les autres et lancera au final la suite de tests.

### Les tests avec `make tests` et `make tests-coverage`
Une autre commande utile : `make tests` ou `make test-coverage`. Ces deux commandes reconstruisent la base de données de test, et lancent la suite de tests sur le projet.


### Problème connus

#### Ports Docker déjà utilisés

```shell
Error response from daemon: driver failed programming external connectivity on endpoint xxx-mailer-1 (9dc1c1f5399ba8ccab04afcde5d5456c71d03b331f47e40c4530d4d6331061fb): 
Bind for 0.0.0.0:1080 failed: port is already allocated
```

Dans ce cas précis, le port demandé par un container docker est déja occupé par une autre application. 
Vous pouvez reconfigurer les ports dans le fichier `docker-composer.override.yml`
