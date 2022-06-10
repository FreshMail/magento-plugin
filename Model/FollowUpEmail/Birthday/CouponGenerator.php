<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Model\FollowUpEmail\Birthday;

use Virtua\FreshMail\Api\FollowUpEmail\Birthday\CouponGeneratorInterface;
use Magento\SalesRule\Api\Data\RuleInterfaceFactory;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Virtua\FreshMail\Model\System\FollowUpEmailBirthdayConfig;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\Data\CouponInterfaceFactory;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Virtua\FreshMail\Model\Config\Source\FollowUpEmail\DiscountType;
use Magento\SalesRule\Model\Coupon\Massgenerator;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;

class CouponGenerator implements CouponGeneratorInterface
{
    private const COUPON_CODE_LENGTH = 12;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var FollowUpEmailBirthdayConfig
     */
    private $followUpEmailBirthdayConfig;

    /**
     * @var CouponInterfaceFactory
     */
    private $couponFactory;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var Massgenerator
     */
    private $codeGenerator;

    /**
     * @var CustomerGroupCollection
     */
    private $customerGroupCollection;

    public function __construct(
        RuleInterfaceFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        FollowUpEmailBirthdayConfig $followUpEmailBirthdayConfig,
        CouponInterfaceFactory $couponFactory,
        CouponRepositoryInterface $couponRepository,
        Massgenerator $codeGenerator,
        CustomerGroupCollection $customerGroupCollection
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
        $this->followUpEmailBirthdayConfig = $followUpEmailBirthdayConfig;
        $this->couponFactory = $couponFactory;
        $this->couponRepository = $couponRepository;
        $this->codeGenerator = $codeGenerator;
        $this->customerGroupCollection = $customerGroupCollection;
    }

    public function generateCouponForCustomer(CustomerInterface $customer): CouponInterface
    {
        $rule = $this->ruleFactory->create();
        $rule->setName($this->getRuleNameForCustomer($customer))
            ->setIsActive(true)
            ->setUsesPerCoupon(1)
            ->setCouponType(RuleInterface::COUPON_TYPE_SPECIFIC_COUPON)
            ->setDiscountAmount($this->followUpEmailBirthdayConfig->getDiscountValue())
            ->setUsesPerCustomer(1)
            ->setSimpleAction($this->getSimpleAction())
            ->setStopRulesProcessing(false)
            ->setFromDate(date('Y-m-d'))
            ->setToDate($this->getCouponToDate())
            ->setCustomerGroupIds($this->getCustomerGroupIds())
            ->setWebsiteIds([$customer->getWebsiteId()]);

        $coupon = $this->couponFactory->create();
        $coupon->setCode($this->getCouponCode())
            ->setType(CouponInterface::TYPE_MANUAL)
            ->setUsageLimit(1)
            ->setUsagePerCustomer(1)
            ->setIsPrimary(true);

        try{
            $rule = $this->ruleRepository->save($rule);

            $coupon->setRuleId($rule->getRuleId());
            $coupon = $this->couponRepository->save($coupon);
        } catch(\Exception $e) {
            // todo handle error
        }

        return $coupon;
    }

    private function getRuleNameForCustomer(CustomerInterface $customer): string
    {
        return __('Birthday coupon ') . date('Y-m-d') . ' ' . $customer->getEmail();
    }

    private function getSimpleAction(): string
    {
        return $this->followUpEmailBirthdayConfig->getDiscountType() === DiscountType::DISCOUNT_TYPE_AMOUNT ?
            RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT : RuleInterface::DISCOUNT_ACTION_BY_PERCENT;
    }

    private function getCouponToDate(): string
    {
        return date(
            'Y-m-d',
            strtotime('+' . $this->followUpEmailBirthdayConfig->getDiscountExpiry() . " days")
        );
    }

    private function getCouponCode(): string
    {
        return $this->codeGenerator
            ->setLength(self::COUPON_CODE_LENGTH)
            ->generateCode();
    }

    private function getCustomerGroupIds(): array
    {
        $customerGroupIds = [];
        $customerGroups = $this->customerGroupCollection->toOptionArray();
        foreach ($customerGroups as $customerGroup) {
            $customerGroupIds[] = $customerGroup['value'];
        }

        return $customerGroupIds;
    }
}
