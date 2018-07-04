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
$obj->createObject('<container-name>','<object-name>','<path-to-the-new-file>','<folder-base>);
```

###### Exemple
Pour créer un fichier qui aura pour référence /img/png/hello.png dans le container "cdn", la commande de création sera la suivante : 

```bash
$obj->createObject('cdn','hello.png','<path-to-the-new-file>','img/png');
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

### Supprimer un objet

```bash
$obj->deleteObject('<container-name>','<filename>');
```

###### Exemple
Pour supprimer le fichier /img/png/hello.png dans le container "cdn", la commande de suppression sera la suivante : 

```bash
$obj->deleteObject('cdn','/img/png/hello.png');
```