<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);
namespace BKozlic\ConfigurableOptions\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Configurable Options Data Helper
 */
class Data
{
    const XML_PATH_ENABLED = 'configurable_options/configurable_general/enabled';
    const XML_PATH_GALLERY = 'configurable_options/configurable_frontend/gallery_switch';
    const XML_PATH_PRESELECTED_ENABLED = 'configurable_options/configurable_frontend/preselected_options';
    const XML_PATH_SIMPLE_UPDATE = 'configurable_options/configurable_frontend/simple_details';
    const XML_PATH_ATTRIBUTES = 'configurable_options/product_attributes/simple_updates';

    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * Data constructor.
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if module is enabled
     *
     * @return boolean
     */
    public function isModuleEnabled()
    {
        return $this->config->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if preselect option is enabled
     *
     * @return boolean
     */
    public function isPreselectEnabled()
    {
        return $this->config->getValue(self::XML_PATH_PRESELECTED_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if update simple detail option is enabled
     *
     * @return boolean
     */
    public function isSimpleProductUpdateEnabled()
    {
        return $this->config->getValue(self::XML_PATH_SIMPLE_UPDATE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns gallery switch strategy
     *
     * @return string
     */
    public function getGallerySwitchStrategy()
    {
        return $this->config->getValue(self::XML_PATH_GALLERY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns attributes data
     *
     * @return string
     */
    public function getSimpleAttributes()
    {
        return $this->config->getValue(self::XML_PATH_ATTRIBUTES, ScopeInterface::SCOPE_STORE);
    }
}
