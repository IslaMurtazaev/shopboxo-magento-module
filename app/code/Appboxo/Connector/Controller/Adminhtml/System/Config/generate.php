<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appboxo\Connector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;

use Magento\Integration\Model\IntegrationFactory;
use Magento\Integration\Model\OauthService;
use Magento\Integration\Model\AuthorizationService;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Api\IntegrationServiceInterface;
use \Magento\Store\Model\StoreManagerInterface;  

class generate extends \Magento\Backend\App\Action
{
    private $resultJsonFactory;

    protected $IntegrationFactory;
    protected $OauthService;
    protected $AuthorizationService;
    protected $Token;
    protected $integrationService;
    protected $storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        IntegrationFactory $IntegrationFactory,
        OauthService $OauthService,
        AuthorizationService $AuthorizationService,
        Token $Token,
        IntegrationServiceInterface $integrationService,
        StoreManagerInterface $storeManager

    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;

        $this->IntegrationFactory = $IntegrationFactory;
        $this->AuthorizationService = $AuthorizationService;
        $this->OauthService = $OauthService;
        $this->Token = $Token;
        $this->integrationService = $integrationService;
        $this->storeManager = $storeManager;
    }
    public function execute()
    {
        $post = $this->getRequest()->getPost();
        $resultJson = $this->resultJsonFactory->create();
        $generated_token = $this->generate_data("info@appboxo.com");
        //$generated_token = $this->get_store_url();
        return $resultJson->setData($generated_token);
    }

    private function generate_data($email){
        //Set your Data
        $name = 'Appboxo Connector';
        $email = $email;
        //$endpoint = 'Set- Url'; (e.g 'http://localhost/magento/')
         
        // Code to check whether the Integration is already present or not
        $integrationExists = $this->IntegrationFactory->create()->load($name,'name')->getData();
        if(empty($integrationExists)){
            $integrationData = array(
                'name' => $name,
                'email' => $email,
                'status' => '1',
                //'endpoint' => $endpoint,
                'setup_type' => '0'
            );
            try{
                $integrationFactory = $this->IntegrationFactory->create();
                $integration = $integrationFactory->setData($integrationData);
                $integration->save();
                $integrationId = $integration->getId();
                $consumerName = 'Integration' . $integrationId;
         
                $consumer = $this->OauthService->createConsumer(['name' => $consumerName]);
                $consumerId = $consumer->getId();
                $integration->setConsumerId($consumer->getId());
                $integration->save();

                $this->AuthorizationService->grantAllPermissions($integrationId);
         
                $uri = $this->Token->createVerifierToken($consumerId);
                $this->Token->setType('access');
                $this->Token->save();
                $int = $this->integrationService->get($integrationId);
                return array("success"=>true,"token"=>$int['token']);
            }catch(Exception $e){
                return array("success"=>false,"message"=>$e->getMessage());
            }
        }else{
            $int = $this->integrationService->get($integrationExists['integration_id']);
            return array("success"=>true,"token"=>$int['token']);
        }  
    }

    private function get_store_url(){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true);
    }
}