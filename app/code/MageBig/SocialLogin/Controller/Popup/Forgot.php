<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\SocialLogin\Controller\Popup;

use MageBig\SocialLogin\Helper\Data;
use Magento\Captcha\Helper\Data as CaptchaData;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Validator\EmailAddress;
use Magento\Framework\Validator\ValidateException;
use Magento\Framework\Validator\ValidatorChain;

/**
 * Class Forgot
 *
 * @package MageBig\SocialLogin\Controller\Popup
 */
class Forgot extends Action
{
    /**
     * @var AccountManagementInterface
     */
    protected $customerManagement;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @type \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @type \Magento\Captcha\Helper\Data
     */
    protected $captchaHelper;

    /**
     * @type \MageBig\SocialLogin\Helper\Data
     */
    protected $socialHelper;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerManagement
     * @param Escaper $escaper
     * @param JsonFactory $resultJsonFactory
     * @param CaptchaData $captchaHelper
     * @param Data $socialHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerManagement,
        Escaper $escaper,
        JsonFactory $resultJsonFactory,
        CaptchaData $captchaHelper,
        Data $socialHelper
    ) {
        $this->session = $customerSession;
        $this->customerManagement = $customerManagement;
        $this->escaper = $escaper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->captchaHelper = $captchaHelper;
        $this->socialHelper = $socialHelper;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function checkCaptcha()
    {
        $formId = 'user_forgotpassword';
        $captchaModel = $this->captchaHelper->getCaptcha($formId);
        if ($captchaModel->isRequired() && !$captchaModel->isCorrect($this->socialHelper->captchaResolve(
                $this->getRequest(),
                $formId
            ))) {
            return false;
        }

        return true;
    }

    /**
     * Exec forgot
     *
     * @return Json
     * @throws ValidateException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        $result = [
            'success' => false,
            'message' => []
        ];

        if (!$this->checkCaptcha()) {
            $result['message'] = __('Incorrect CAPTCHA.');

            return $resultJson->setData($result);
        }

        $email = (string)$this->getRequest()->getPost('email');
        if ($email) {
            if (!ValidatorChain::is($email, EmailAddress::class)) {
                $this->session->setForgottenEmail($email);
                $result['message'][] = __('Please correct the email address.');
            }

            try {
                $this->customerManagement->initiatePasswordReset(
                    $email,
                    AccountManagement::EMAIL_RESET
                );
                $result['success'] = true;
                $result['message'][] = __(
                    'If there is an account associated with %1 you will receive an email with a link to reset your password.',
                    $this->escaper->escapeHtml($email)
                );
            } catch (NoSuchEntityException $e) {
                $result['success'] = true;
                $result['message'][] = __(
                    'If there is an account associated with %1 you will receive an email with a link to reset your password.',
                    $this->escaper->escapeHtml($email)
                );
                // Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
            } catch (SecurityViolationException $exception) {
                $result['error'] = true;
                $result['message'][] = $exception->getMessage();
            } catch (\Exception $exception) {
                $result['error'] = true;
                $result['message'][] = __('We\'re unable to send the password reset email.');
            }
        }

        return $resultJson->setData($result);
    }
}
