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
];

$obj = new Glibe\StorageConnetOvh($credentials);
```

### Récupération infos d'une ressource

```bash
$obj->getObject('<container-name>','<object-name>');
```

### Création d'une ressource

```bash
$obj->createObject('<container-name>','<object-name>','<path-to-the-new-file>');
```

### Création d'une container

Statut possible : public / private

En public cela ajoute des metadatas pour faire du container un hébergement statique. (Voir site ovh)

```bash
$obj->createContainer('<container-name>','<statut>');
```

### Accès à un container
Renvoie une instance du container souhaité

```bash
$obj->getContainer('<container-name>');
```