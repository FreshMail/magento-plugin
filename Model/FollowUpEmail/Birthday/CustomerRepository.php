<?php

declare(strict_types=1);

namespace Virtua\FreshMail\Model\FollowUpEmail\Birthday;

use Magento\Customer\Api\Data\CustomerInterface;
use Virtua\FreshMail\Api\FollowUpEmail\Birthday\CustomerRepositoryInterface;
use Virtua\FreshMail\Model\System\FollowUpEmailBirthdayConfig;
use Magento\Customer\Api\CustomerRepositoryInterface as CoreCustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Virtua\FreshMail\Model\Config\Source\FollowUpEmail\DiscountEmailDaySend;

class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * @var FollowUpEmailBirthdayConfig
     */
    private $followUpEmailBirthdayConfig;

    /**
     * @var CoreCustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        FollowUpEmailBirthdayConfig $followUpEmailBirthdayConfig,
        CoreCustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->followUpEmailBirthdayConfig = $followUpEmailBirthdayConfig;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return CustomerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomersForBirthdayFollowUp(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('dob', $this->getDateFilterForBirthdayFollowUp(), 'like')
            ->create();

        return $this->customerRepository->getList($searchCriteria)->getItems();
    }

    private function getDateFilterForBirthdayFollowUp(): string
    {
        $date = date('%-m-d');
        if ($this->followUpEmailBirthdayConfig->getEmailDaySend() === DiscountEmailDaySend::SEND_ONE_DAY_BEFORE_BIRTHDAY) {
            $date = date('%-m-d', strtotime("+1 days"));
        }

        return $date;
    }
}
