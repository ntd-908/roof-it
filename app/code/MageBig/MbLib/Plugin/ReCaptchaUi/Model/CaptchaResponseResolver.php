<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageBig\MbLib\Plugin\ReCaptchaUi\Model;

use Magento\Framework\App\RequestInterface;

class CaptchaResponseResolver
{
    public const PARAM_RECAPTCHA = 'g-recaptcha-response';

    /**
     * Fix recaptcha throw error.
     *
     * @param $subject
     * @param $proceed
     * @param RequestInterface $request
     * @return string
     */
    public function aroundResolve($subject, $proceed, RequestInterface $request): string
    {
        $reCaptchaParam = $request->getParam(self::PARAM_RECAPTCHA);
        if (empty($reCaptchaParam)) {
            return '';
        }
        return $reCaptchaParam;
    }
}