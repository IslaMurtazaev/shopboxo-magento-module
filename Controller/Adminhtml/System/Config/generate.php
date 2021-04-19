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
                $resources = ['Magento_Sales::sales','Magento_Sales::sales_operation','Magento_Sales::sales_order','Magento_Sales::actions','Magento_Sales::create', 'Magento_Sales::actions_view', 'Magento_Sales::reorder', 'Magento_Sales::actions_edit','Magento_Sales::cancel','Magento_Sales::review_payment','Magento_Sales::capture','Magento_Sales::invoice','Magento_Sales::comment','Magento_Catalog::catalog','Magento_Catalog::catalog_inventory','Magento_Catalog::products','Magento_Catalog::update_attributes','Magento_Catalog::edit_product_design','Magento_Catalog::categories','Magento_Catalog::edit_category_design','Magento_Customer::customer','Magento_Customer::manage','Magento_Customer::actions','Magento_Customer::delete','Magento_Customer::reset_password','Magento_Customer::invalidate_tokens','Magento_LoginAsCustomer::allow_shopping_assistance','Magento_Cart::cart','Magento_Cart::manage','Magento_Backend::stores','Magento_Backend::stores_settings','Magento_Config::config','Magento_CatalogInventory::cataloginventory'];
    
                $integrationFactory = $this->IntegrationFactory->create();
                $integration = $integrationFactory->setData($integrationData);
                $integration->save();
                $integrationId = $integration->getId();
                $consumerName = 'Integration' . $integrationId;
         
                $consumer = $this->OauthService->createConsumer(['name' => $consumerName]);
                $consumerId = $consumer->getId();
                $integration->setConsumerId($consumer->getId());
                $integration->save();

                $this->AuthorizationService->grantPermissions($integrationId, $resources);

                //$this->AuthorizationService->grantAllPermissions($integrationId);
         
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