# GUCE

## Développement

Il est recommandé l'installation de [Git LFS](https://git-lfs.github.com/)

## Docker infrastructure

First start:

```sh
> docker-compose --env-file ./docker/.env build
> docker-compose --env-file ./docker/.env up -d db
> docker-compose --env-file ./docker/.env up -d nginx
```

## Backend

- Langue: PHP
- Cadre: Lumen

Avant de vous engager, validez le code avec des outils qualité:

```sh
> composer run-script qa
```

### PHP Tools

- PHP CodeSniffer

  ```sh
  > php vendor/bin/phpcs
  ```

- PHP CodeSniffer fixer

  ```sh
  > php vendor/bin/phpcbf
  ```

- PHP Mess Detector (unsoported PHP 8)

  ```sh
  > php vendor/bin/phpmd
  ```

- PHP Coding Standards Fixer

  ```sh
  > php vendor/bin/php-cs-fixer
  ```

- Analyse code for bugs

  ```sh
  > php vendor/bin/phpstan analyse
  ```
