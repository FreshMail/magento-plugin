<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Model\System;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class FollowUpEmailBirthdayConfig
{
    private const CONFIG_FUE_BIRTHDAY_ENABLED = 'follow_up_email/birthday/enabled';
    private const CONFIG_FUE_BIRTHDAY_DISCOUNT_TYPE = 'follow_up_email/birthday/discount_type';
    private const CONFIG_FUE_BIRTHDAY_DISCOUNT_VALUE = 'follow_up_email/birthday/discount_value';
    private const CONFIG_FUE_BIRTHDAY_DISCOUNT_EXPIRY = 'follow_up_email/birthday/discount_expiry';
    private const CONFIG_FUE_BIRTHDAY_DISCOUNT_USAGE = 'follow_up_email/birthday/discount_usage';
    private const CONFIG_FUE_BIRTHDAY_EMAIL_DAY_SEND = 'follow_up_email/birthday/email_day_send';
    private const CONFIG_FUE_BIRTHDAY_EMAIL_TEMPLATE = 'follow_up_email/birthday/email_template';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        EncryptorInterface $encryptor,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->encryptor = $encryptor;
        $this->scopeConfig = $scopeConfig;
    }

    private function getStoreConfig(string $path, ?int $store = null): ?string
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getIsEmailEnabled(?int $storeId = null): bool
    {
        return (bool) $this->getStoreConfig(self::CONFIG_FUE_BIRTHDAY_ENABLED, $storeId);
    }

    public function getDiscountType(?int $storeId = null): int
    {
        return (int) $this->getStoreConfig(self::CONFIG_FUE_BIRTHDAY_DISCOUNT_TYPE, $storeId);
    }

    public function getDiscountValue(?int $storeId = null): int
    {
        return (int) $this->getStoreConfig(self::CONFIG_FUE_BIRTHDAY_DISCOUNT_VALUE, $storeId);
    }

    public function getDiscountExpiry(?int $storeId = null): int
    {
        return (int) $this->getStoreConfig(self::CONFIG_FUE_BIRTHDAY_DISCOUNT_EXPIRY, $storeId);
    }

    public function getDiscountUsage(?int $storeId = null): int
    {
        return (int) $this->getStoreConfig(self::CONFIG_FUE_BIRTHDAY_DISCOUNT_USAGE, $storeId);
    }

    public function getEmailDaySend(?int $storeId = null): int
    {
        return (int) $this->getStoreConfig(self::CONFIG_FUE_BIRTHDAY_EMAIL_DAY_SEND, $storeId);
    }

    public function getEmailTemplate(?int $storeId = null): int
    {
        return (int) $this->getStoreConfig(self::CONFIG_FUE_BIRTHDAY_EMAIL_TEMPLATE, $storeId);
    }
}
