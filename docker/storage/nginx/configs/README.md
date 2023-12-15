# Structure exemple

```text
.
├── README.md
├── nginx.conf
├── conf.d
│   ├── 00-general.conf         : Configuration générale
│   ├── 10-gzip.conf            : Paramètres de compression
│   └── 20-upstreem-php.conf    : Upstream de PHP
├── default.d
│   └── files.conf              : Paramètres généraux des fichiers (robots, favicon, etc...)
├── locations.d
│   ├── lumen.conf              : Emplacement nommé pour la gestion en Upstream
│   └── public-files.conf       : Accès au stockage alternatif
└── servers.d
    └── 00-default.conf         : Serveur par défaut
```
