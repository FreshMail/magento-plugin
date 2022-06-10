<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Api\FollowUpEmail\Birthday;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\SalesRule\Api\Data\CouponInterface;

interface CouponGeneratorInterface
{
    public function generateCouponForCustomer(CustomerInterface $customer): CouponInterface;
}
