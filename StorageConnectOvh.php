<?php

namespace Glibe;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Stream;
use OpenStack\Common\Transport\Utils as TransportUtils;
use OpenStack\Identity\v2\Service;
use OpenStack\OpenStack;



class StorageConnetOvh {

    private $authUrl    = null;
    private $region     = null;
    private $username   = null;
    private $password   = null;
    private $tenantName = null;

    public $openstack               = null;
    public $currentContainerName    = null;
    public $currentContainer        = null;

    public function __construct($credentials) {

        $this->authUrl      = $credentials['authUrl'];
        $this->region       = $credentials['region'];
        $this->username     = $credentials['username'];
        $this->password     = $credentials['password'];
        $this->tenantName   = $credentials['tenantName'];
        /*
        *
        * AUTHENTIFICATION 
        *
        */

        $httpClient = new Client([
            'base_uri' => TransportUtils::normalizeUrl($this->authUrl),
            'handler'  => HandlerStack::create(),
        ]);
        
        $options = [
            'authUrl'         => $this->authUrl,
            'region'          => $this->region,
            'username'        => $this->username,
            'password'        => $this->password,
            'tenantName'      => $this->tenantName,
            'identityService' => Service::factory($httpClient),
        ];

        $this->openstack = new OpenStack($options);

    }

    /*
    * Créer un container
    * Statut public ou private
    */

    public function createContainer($container_name,$statut) {
    
        $creation  = $this->openstack->objectStoreV1()->createContainer(
            ['name' => $container_name]);
        
        if ($statut == 'public') {
            $options = [
                'Web-Listings'      => "true",
                'Web-Error'         => "error.html",
                'Web-Listings-Css'  => "listing.css",
                'Web-Index'         => "index.html"
            ];

           $creation->resetMetadata($options); 
        }

        return $creation;

    }

    /*
    * Récupérer un container
    * Statut public ou private
    */

    public function getContainer($container_name) {
    
        if ($this->currentContainerName !== null && $this->currentContainerName == $container_name) {
            return $this->currentContainer;
        } else {
            $container = $this->openstack->objectStoreV1()
                        ->getContainer($container_name);
            
            if ($container) {
                $this->currentContainerName = $container_name;
                $this->currentContainer = $container;
                return $this->currentContainer;
            } else {
                return false;
            }
        }

    }

    /*
    * Récupérer un objet (ressource image , pdf ...) dans un container donné
    * 
    */

    public function getObject($container,$objet) {
        $object = $this->getContainer($container)->getObject($objet);
        $object->retrieve();

        return $object;
    }

    /*
    * Créer un objet (ressource image , pdf ...) dans un container donné
    * Param 2 : le nom de l'objet (ex images/png/logo.png)
    * Param 3 : le chemin vers la ressource pour import
    */
    
    public function createObject($container,$filename,$filepath) {

        //Création du stream
        $stream = new Stream(fopen($filepath, 'r'));

        $options = [
            'name'   => $filename,
            'stream' => $stream,
        ];

        //Création objet
        /** @var \OpenStack\ObjectStore\v1\Models\StorageObject $object */
        $object = $this->getContainer($container)->createObject($options);

        return $object;

    }
}
