<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Cron\FollowUpEmail;

use Magento\Framework\Intl\DateTimeFactory;
use Magento\Store\Model\StoreManagerInterface;
use Virtua\FreshMail\Api\Data\FollowUpEmailInterface;
use Virtua\FreshMail\Api\Data\FollowUpEmailInterfaceFactory;
use Virtua\FreshMail\Api\FollowUpEmailRepositoryInterface;
use Virtua\FreshMail\Logger\Logger;
use Virtua\FreshMail\Model\System\FollowUpEmailBirthdayConfig;
use Virtua\FreshMail\Api\FollowUpEmail\Birthday\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Virtua\FreshMail\Api\FollowUpEmail\Birthday\CouponGeneratorInterface;

class Birthday
{
    private const SCHEDULED_AT_HOUR_AND_MINUTE = '08:00:00';

    /**
     * @var int|null
     */
    private $currentStoreId;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    /**
     * @var FollowUpEmailBirthdayConfig
     */
    private $followUpEmailBirthdayConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FollowUpEmailRepositoryInterface
     */
    private $followUpEmailRepository;

    /**
     * @var FollowUpEmailInterfaceFactory
     */
    private $followUpEmailFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CouponGeneratorInterface
     */
    private $couponGenerator;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        DateTimeFactory $dateTimeFactory,
        FollowUpEmailBirthdayConfig $followUpEmailBirthdayConfig,
        FollowUpEmailRepositoryInterface $followUpEmailRepository,
        StoreManagerInterface $storeManager,
        FollowUpEmailInterfaceFactory $followUpEmailFactory,
        CustomerRepositoryInterface $customerRepository,
        CouponGeneratorInterface $couponGenerator,
        Logger $logger
    ) {
        $this->dateTimeFactory = $dateTimeFactory;
        $this->followUpEmailBirthdayConfig = $followUpEmailBirthdayConfig;
        $this->followUpEmailRepository = $followUpEmailRepository;
        $this->storeManager = $storeManager;
        $this->followUpEmailFactory = $followUpEmailFactory;
        $this->customerRepository = $customerRepository;
        $this->couponGenerator = $couponGenerator;
        $this->logger = $logger;
    }

    public function execute(): void
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $this->setCurrentStoreId((int) $store->getId());
            $this->searchAndAddToQueueEmailIfEnabled();
        }
    }

    private function setCurrentStoreId(int $storeId): void
    {
        $this->currentStoreId = $storeId;
    }

    private function getCurrentStoreId(): ?int
    {
        return $this->currentStoreId;
    }

    private function searchAndAddToQueueEmailIfEnabled(): void
    {
        if ($this->followUpEmailBirthdayConfig->getIsEmailEnabled($this->getCurrentStoreId())) {
            $customers = $this->customerRepository->getCustomersForBirthdayFollowUp();
            $this->addEmailsToQueueFromCustomers($customers);
        }
    }

    /**
     * @param CustomerInterface[] $customers
     */
    private function addEmailsToQueueFromCustomers(array $customers): void
    {
        foreach ($customers as $customer) {
            $this->addEmailToQueue($customer);
        }
    }

    private function addEmailToQueue(CustomerInterface $customer): void
    {
        try {
            $coupon = $this->couponGenerator->generateCouponForCustomer($customer);

            /** @var FollowUpEmailInterface $followUpEmail */
            $followUpEmail = $this->followUpEmailFactory->create();
            $followUpEmail->setCustomerId((int) $customer->getId());
            $followUpEmail->setCustomerEmail($customer->getEmail());
            $followUpEmail->setTemplateId($this->followUpEmailBirthdayConfig->getEmailTemplate());
            $followUpEmail->setScheduledAt($this->getScheduledAt());
            $followUpEmail->setStoreId($this->getCurrentStoreId());
            $followUpEmail->setType(FollowUpEmailInterface::TYPE_BIRTHDAY);
            $followUpEmail->setConnectedEntityId((int) $coupon->getCouponId());

            $this->followUpEmailRepository->save($followUpEmail);

        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    private function getScheduledAt(): string
    {
        $scheduledAt = $this->dateTimeFactory->create();
        return $scheduledAt->format('Y-m-d') . ' ' . self::SCHEDULED_AT_HOUR_AND_MINUTE;
    }
}
