# Connecteur PHP - Object Storage OVH

## Install

```bash
composer require glibe/storageconnect-ovh
```
### Utilisation

Les éléments de connexion à openstack sont obligatoires. Les élèments de connexion à l'API d'OVH sont optionnels. Ils sont utilisés uniquement dans le cas d'une création de container. 

La création des credentials pour utiliser l'api d'ovh se fait à cette url : [https://api.ovh.com/createToken/index.cgi?GET=/*&PUT=/*&POST=/*&DELETE=/*](https://api.ovh.com/createToken/index.cgi?GET=/*&PUT=/*&POST=/*&DELETE=/*)

```bash
$credentials = [
    'authUrl'         => "https://auth.cloud.ovh.net/v2.0",
    'region'          => "GRA3",
    'username'        => "your-username",
    'password'        => "your-password",
    'tenantName'      => "your-tenant-name",
];

$ovhCredentials = [
    'application_key' => "XXXXXXXX",
    'application_secret' => "XXXXXXXX",
    'api_endpoint' => "XXXXXXXX", // ovh-eu for europe
    'consumer_key' => "XXXXXXXX"
];

$obj = new Glibe\StorageConnetOvh($credentials,$ovhCredentials);
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

####### Statut possible : public / private

En public cela fait container un hébergement statique. Les ressources seront alors accessiblent par une url publique. (Voir site ovh pour plus d'explication.) Par défaut le statut est à private.

####### Ovh project ID
L'identifiant unique de votre projet public cloud chez OVH. Disponible sous le nom de votre proejt dans votre interface client.  Si non définit, le premier projet disponible sera utilisé.

```bash
$obj->createContainer('<container-name>','<statut> (default: private)','<ovh_project_id> (default: first available)');
```

###### Exemple

```bash
$obj->createContainer('testContainer','public','90b0b09604e74e4e8ade65xxxxxxxxx');
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