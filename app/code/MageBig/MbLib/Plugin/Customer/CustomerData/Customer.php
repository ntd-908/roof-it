<?php

namespace MageBig\MbLib\Plugin\Customer\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View;

class Customer
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var View
     */
    private $customerViewHelper;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param View $customerViewHelper
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        View $customerViewHelper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerViewHelper = $customerViewHelper;
    }

    /**
     * Get customer data
     *
     * @param \Magento\Customer\CustomerData\Customer $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, array $result)
    {
        if ($result) {
            $customer = $this->currentCustomer->getCustomer();
            $result['email'] = $customer->getEmail();
            $result['lastname'] = $customer->getLastname();
        }

        return $result;
    }
}
