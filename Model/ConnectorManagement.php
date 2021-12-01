<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Model;

use Appboxo\Connector\Api\ConnectorManagementInterface;
use Appboxo\Connector\Model\Data\Connector;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartRepositoryInterface;
use Appboxo\Connector\Api\Data\ConnectorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order;
//use \Magento\Framework\App\RequestInterface;

class ConnectorManagement implements ConnectorManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var request
     */
    //protected $request;
    
    /** @var OrderInterface */
    protected $orderResourceModel;


    /** @var OrderRepository */
    protected $orderRepository;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Order $orderResourceModel,
        OrderRepositoryInterface $orderRepository,
        ScopeConfigInterface $scopeConfig
        //RequestInterface $request
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->scopeConfig = $scopeConfig;
        $this->orderResourceModel = $orderResourceModel;
        $this->orderRepository = $orderRepository;
        //$this->request = $request;
    }

    /**
     * @param int $cartId
     * @param ConnectorInterface $orderPaymentId
     * @return null|string
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveConnector(
        $cartId,
        ConnectorInterface $orderPaymentId
    ) {
         $quote = $this->quoteRepository->getActive($cartId);
         
        if (!$quote->getItemsCount()) {
              throw new NoSuchEntityException(
                  __('Cart %1 doesn\'t contain products', $cartId)
              );
        }
        
        $paymentid = $orderPaymentId->getPaymentId();

        $this->validatePaymentId($paymentid);
        if($paymentid == null){
            //$paymentid = $this->request->getParams()['orderPaymentId'];
            $paymentid_request = json_decode(file_get_contents('php://input'),true);
            $paymentid = $paymentid_request['orderPaymentId'];
        } 
        
        try {
             $quote->setData(Connector::COMMENT_FIELD_NAME, strip_tags($paymentid));
            
             $quote->save();
        } catch (\Exception $e) {
               throw new CouldNotSaveException(
                   __('The order paymentid could not be saved')
               );
        }

         return $paymentid;
    }


    public function saveOderComment(
        $orderId,
        ConnectorInterface $orderPaymentId
    ) {

        try {
            $order = $this->orderRepository->get($orderId);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __("Please enter a valid order ID")
            );
        }

        $paymentid_order = $orderPaymentId->getPaymentId();

        $this->validatePaymentId($paymentid_order);
        if($paymentid_order == null){
            $paymentid_request = json_decode(file_get_contents('php://input'),true);
            $paymentid_order = $paymentid_request['orderPaymentId'];
        } 

        try {
            $order->setOrderPaymentid($paymentid_order);
            $this->orderResourceModel->save($order);

        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __("The order ID could not be saved")
            );
        }
        return $paymentid_order;
    }

    /**
     * @param string $paymentid
     * @throws ValidatorException
     */
    protected function validatePaymentId($paymentid)
    {
        $maxPaymentIdLength = $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_GENERAL_MAX_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
        
        if ($maxPaymentIdLength && (mb_strlen($paymentid) > $maxPaymentIdLength)) {
            throw new ValidatorException(
                __('The order paymentid entered exceeded the limit')
            );
        }
    }
}
