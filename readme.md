# Connecteur PHP - Object Storage OVH

## Install

```bash
composer require glibe/storageconnect-ovh
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
$api->getObject('<container-name>','<object-name>');
```

### Exemple création d'une ressource
Ici cdn est à remplacer par le token du site car c'est le nom du container

```bash
$api->createObject('<container-name>','<object-name>','<path-to-the-new-file>');
```