<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Model\Config\Source\FollowUpEmail;

use Magento\Framework\Data\OptionSourceInterface;

class DiscountType implements OptionSourceInterface
{
    public const DISCOUNT_TYPE_AMOUNT = 1;
    public const DISCOUNT_TYPE_PERCENTAGE = 2;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::DISCOUNT_TYPE_AMOUNT,
                'label' => __('Amount')
            ],
            [
                'value' => self::DISCOUNT_TYPE_PERCENTAGE,
                'label' => __('Percentage')
            ]
        ];
    }
}
