<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Api\FollowUpEmail\Birthday;

use Magento\Customer\Api\Data\CustomerInterface;

interface CustomerRepositoryInterface
{
    /**
     * @return CustomerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomersForBirthdayFollowUp(): array;
}
