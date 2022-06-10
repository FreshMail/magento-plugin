<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Model\Config\Source\FollowUpEmail;

use Magento\Framework\Data\OptionSourceInterface;

class DiscountEmailDaySend implements OptionSourceInterface
{
    public const SEND_ONE_DAY_BEFORE_BIRTHDAY = 1;
    public const SEND_ON_BIRTHDAY_DAY = 2;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::SEND_ONE_DAY_BEFORE_BIRTHDAY,
                'label' => __('One day before birthday')
            ],
            [
                'value' => self::SEND_ON_BIRTHDAY_DAY,
                'label' => __('On birthday day')
            ]
        ];
    }
}
