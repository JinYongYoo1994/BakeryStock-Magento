<?php

namespace Pimgento\Api\Helper;

use Magento\Framework\App\Helper\Context;
use Pimgento\Api\Helper\Authenticator as Authenticator;
use Pimgento\Api\Helper\Data as Helper;

/**
 * Class LocalesHelper
 *
 * @category  Class
 * @package   Pimgento\Api\Helper
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2019 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class Locales extends Helper
{
    /**
     * This variable contains a Authenticator
     *
     * @var \Pimgento\Api\Helper\Authenticator $authenticator
     */
    protected $authenticator;

    /**
     * Locales constructor
     *
     * @param Context       $context
     * @param Authenticator $authenticator
     */
    public function __construct(
        Context $context,
        Authenticator $authenticator
    ) {
        parent::__construct($context);

        $this->authenticator = $authenticator;
    }

    /**
     * Get active Akeneo locales
     *
     * @return string[]
     * @throws Pimgento_Api_Exception
     */
    public function getAkeneoLocales()
    {
        /** @var Akeneo\Pim\ApiClient\AkeneoPimClientInterface|Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface $apiClient */
        $apiClient = $this->authenticator->getAkeneoApiClient();
        /** @var \Akeneo\Pim\ApiClient\Api\LocaleApiInterface $localeApi */
        $localeApi = $apiClient->getLocaleApi();
        /** @var Akeneo\Pim\ApiClient\Pagination\ResourceCursorInterface $locales */
        $locales = $localeApi->all(
            10,
            [
                'search' => [
                    'enabled' => [
                        [
                            'operator' => '=',
                            'value'    => true,
                        ],
                    ],
                ],
            ]
        );

        /** @var string[] $akeneoLocales */
        $akeneoLocales = [];
        /** @var mixed[] $locale */
        foreach ($locales as $locale) {
            if (empty($locale['code'])) {
                continue;
            }
            $akeneoLocales[] = $locale['code'];
        }

        return $akeneoLocales;
    }
}
