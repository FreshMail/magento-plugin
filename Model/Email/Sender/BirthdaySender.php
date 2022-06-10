<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Model\Email\Sender;

use \Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Virtua\FreshMail\Model\Email\Sender;
use Virtua\FreshMail\Api\Email\SenderInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\Framework\DataObject;


class BirthdaySender extends Sender implements SenderInterface
{
    /**
     * @var CouponInterface|null
     */
    private $coupon;

    /**
     * @var CustomerInterface|null
     */
    private $customer;

    public function send(): void
    {
        try {
            $this->checkAndSend();
        } catch (Exception $e){
            //$e->getMessage();
            //todo handle exception
        }
    }


    protected function prepareTemplate(): void
    {
        $transport = [
            'coupon' => $this->coupon,
            'customer_firstname' => $this->customer->getFirstname(),
            'customer_lastname' => $this->customer->getLastname(),
            'customer_name' => $this->customer->getFirstname() . ' ' . $this->customer->getLastname()
        ];

        $transportObject = new DataObject($transport);
        $this->templateContainer->setTemplateVars($transportObject->getData());

        parent::prepareTemplate();
    }

    public function setCoupon(CouponInterface $coupon): void
    {
        $this->coupon = $coupon;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }
}
