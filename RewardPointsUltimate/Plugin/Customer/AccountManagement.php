<?php


namespace Mageplaza\RewardPointsUltimate\Plugin\Customer;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Mageplaza\RewardPointsUltimate\Helper\Data as HelperData;
use Mageplaza\RewardPointsUltimate\Model\BehaviorFactory;
use Mageplaza\RewardPointsUltimate\Model\Source\CustomerEvents;

class AccountManagement
{

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var BehaviorFactory
     */
    protected $behaviorFactory;

    /**
     * CustomerRegisterSuccess constructor.
     *
     * @param HelperData $helperData
     * @param BehaviorFactory $behaviorFactory
     */
    public function __construct(
        HelperData $helperData,
        BehaviorFactory $behaviorFactory
    ) {
        $this->helperData      = $helperData;
        $this->behaviorFactory = $behaviorFactory;
    }

    public function afterCreateAccount(
        \Magento\Customer\Model\AccountManagement $subject,
        $result,
        $customer,
        $password = null,
        $redirectUrl = ''
    ) {

        $isMobile = ((int) $customer->getExtensionAttributes()->getIsMobile());
        if ($isMobile == 1) {
            if ($this->helperData->isEnabled()) {
                $this->behaviorSignUp($result);
                $this->behaviorNewLetter($result);
                $this->setCookieReferer($customer);
            }
        }
        return $result;
    }

    /**
     * @param Object $customer
     *
     * @throws LocalizedException
     */
    public function behaviorSignUp($customer)
    {
        $pointSignUp = $this->behaviorFactory->create()->getPointByAction(CustomerEvents::SIGN_UP);
        $expireAfter = $this->behaviorFactory->create()->getExpireAfterByAction(CustomerEvents::SIGN_UP);
        if ($pointSignUp) {
            $this->helperData->getTransaction()->createTransaction(
                HelperData::ACTION_SIGN_UP,
                $customer,
                new DataObject([
                    'point_amount' => $pointSignUp,
                    'expireAfter'  => $expireAfter
                ])
            );
        }
    }

    /**
     * @param Object $customer
     *
     * @throws LocalizedException
     */
    public function behaviorNewLetter($customer)
    {
        $pointSubscriber   = $this->behaviorFactory->create()->getPointByAction(CustomerEvents::NEWSLETTER);
        $expireAfter       = $this->behaviorFactory->create()->getExpireAfterByAction(CustomerEvents::NEWSLETTER);

        if (((int) $customer->getExtensionAttributes()->getIsSubscribed()) == 1 && $pointSubscriber) {
            $this->helperData->getTransaction()->createTransaction(
                HelperData::ACTION_NEWSLETTER,
                $customer,
                new DataObject([
                    'point_amount' => $pointSubscriber,
                    'expireAfter'  => $expireAfter
                ])
            );
        }
    }

    /**
     * @param Object $customer
     *
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    public function setCookieReferer($customer)
    {
        $referCodeOrEmail  = trim($customer->getExtensionAttributes()->getMpRefer());

        try {
            $referCode = $this->helperData->getCryptHelper()->checkReferCodeOrEmail($referCodeOrEmail);
        } catch (Exception $e) {
            $referCode = false;
        }

        if ($referCode) {
            $this->helperData->getCookieHelper()->set($referCode);
        }
    }
}
