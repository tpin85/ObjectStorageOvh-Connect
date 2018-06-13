# Connecteur PHP - Object Storage OVH

## Install

```bash
composer require 'GlibeFr\'
```
### Utilisation

```bash
$credentials = [
    'authUrl'         => "https://auth.cloud.ovh.net/v2.0",
    'region'          => "GRA3",
    'username'        => "your-username",
    'password'        => "your-password",
    'tenantName'      => "your-tenant-name",
]

new Glibe\StorageConnetOvh($credentials)
```

### Exemple récupération info d'une ressource

```bash
$api->getObjet('cdn','togo.jpg');
```

### Exemple création d'une ressource
Ici cdn est à remplacer par le token du site car c'est le nom du container

```bash
$api->createObject('cdn','images/png/toto.png','files/togo.jpg');
```