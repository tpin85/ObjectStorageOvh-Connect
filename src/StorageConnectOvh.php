<?php

namespace Glibe;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Stream;
use OpenStack\Common\Transport\Utils as TransportUtils;
use OpenStack\Identity\v2\Service;
use OpenStack\OpenStack;
use Cocur\Slugify\Slugify;
use Ovh\Api;

class StorageConnectOvh {

    private $authUrl    = null;
    private $region     = null;
    private $username   = null;
    private $password   = null;
    private $tenantName = null;

    public $openstack               = null;
    public $currentContainerName    = null;
    public $currentContainer        = null;

    // OVH

    public $ovh = null;

    public function __construct($credentials, $ovhCredentials = null) {

        $this->authUrl      = $credentials['authUrl'];
        $this->region       = $credentials['region'];
        $this->username     = $credentials['username'];
        $this->password     = $credentials['password'];
        $this->tenantName   = $credentials['tenantName'];
        /*
        *
        * AUTHENTIFICATION API OPENSTACK -- v3
        *
        */
        
        $options = [
            'authUrl'         => $this->authUrl,
            'region'          => $this->region,
            'user'            => [
                'name'            => $this->username,
                'domain'        => ['id' => 'default'],
                'password'      => $this->password
            ],
            'scope'           => [
                'project'       => [
                    'name'          => $this->tenantName,
                    'domain'        => ['id' => 'default']
                    ]]
        ];

        $this->openstack = new OpenStack($options);

        /*
        *
        * AUTHENTIFICATION API OVH
        *
        */

        if (!is_null($ovhCredentials)) {
            $this->ovh = new Api( 
                $ovhCredentials['application_key'],  // Application Key
                $ovhCredentials['application_secret'],  // Application Secret
                $ovhCredentials['api_endpoint'],      // Endpoint of API OVH Europe (List of available endpoints)
                $ovhCredentials['consumer_key']); // Consumer Key
    
        }

    }

    /*
    * Créer un container
    * Statut public ou private - via OVH API
    */

    public function createContainer($container_name, $statut = 'private', $ovh_project_id = null) {
    
        if ($this->ovh !== null) {
            //Project id - if null, first ovh project is use.
            if (is_null($ovh_project_id)) {
                $projects = $this->ovh->get('/cloud/project');
                $ovh_project_id = $projects[0];
            }

            // Container existe t'il déjà ?
            $storageContainers = $this->ovh->get('/cloud/project/' . $ovh_project_id . '/storage');

            $containerExist = false;

            foreach ($storageContainers as $storageContainer) {
                if ($storageContainer['name'] == $container_name) {
                    $containerId = $storageContainer['id'];
                    $containerExist = true;
                    break; 
                }
            }

            if (!$containerExist) {
                $create = $this->ovh->post('/cloud/project/' . $ovh_project_id . '/storage', array(
                    'archive' => false, // Archive container flag (type: boolean)
                    'containerName' => $container_name, // Container name (type: string)
                    'region' => $this->region, // Region (type: string)
                ));
                $create['create'] = true;

                if ($statut == 'public') {
                    $this->ovh->post('/cloud/project/' . $ovh_project_id . '/storage/' . $create['id'] . '/static');
                    $create['public'] = true;
    
                } else {
                    $create['public'] = false;
                }

                return json_encode($create);
    
            } else {

                return json_encode([
                    'create' => false,
                    'container_id' => $containerId,
                    'result' => 'already exist'
                ]);
            }
           
            

        } else {
            return json_encode([
                'create' => false,
                'result' => 'OVH API NOT AVAILABLE - OVH cred not send'
            ]);
        }


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
    
    public function createObject($container,$filename,$filepath,$folder='') {

        //Création du stream
        $stream = new Stream(fopen($filepath, 'r'));
        $slugify = new Slugify();

        $files = explode('.',$filename);
        if ($folder == '') {
            $name = $files[0] . '.' . $files[1];
        } else {
            $name = trim($folder,'/') . '/' . $files[0] . '.' . $files[1];
        }

        $options = [
            'name'   => $name,
            'stream' => $stream,
        ];

        // Création objet
        /** @var \OpenStack\ObjectStore\v1\Models\StorageObject $object */
        $object = $this->getContainer($container)->createObject($options);

        // Fermeture du stream
        $stream->close();
        
        return $object;

    }

    /*
    * Supprimer un objet (ressource image , pdf ...) dans un container donné
    * 
    */

    public function deleteObject($container,$filename) {
        return $this->getObject($container,$filename)->delete();
    }

    public function getMeta($container) {
        $object = $this->openstack->objectStoreV1();

        return $object->getAccount();
    }

    public function downloadObject($container,$objet) {
        /** @var \GuzzleHttp\Stream\Stream $stream */
        $stream = $this->openstack->objectStoreV1()
        ->getContainer($container)->getObject($objet)->download();
        //$stream->download();
        return $stream;
    }

}
