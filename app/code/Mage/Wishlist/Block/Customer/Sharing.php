<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist customer sharing block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Sharing extends Mage_Core_Block_Template
{
    /**
     * Entered Data cache
     *
     * @param array
     */
    protected $_enteredData = null;

    /**
     * Wishlist configuration
     *
     * @var Mage_Wishlist_Model_Config
     */
    protected $_wishlistConfig;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Wishlist_Model_Config $wishlistConfig
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Wishlist_Model_Config $wishlistConfig,
        array $data = array()
    ) {
        $this->_wishlistConfig = $wishlistConfig;
        parent::__construct($context, $data);
    }

    /**
     * Prepare Global Layout
     *
     * @return Mage_Wishlist_Block_Customer_Sharing
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Wish List Sharing'));
        }
    }

    /**
     * Retrieve Send Form Action URL
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('*/*/send');
    }

    /**
     * Retrieve Entered Data by key
     *
     * @param string $key
     * @return mixed
     */
    public function getEnteredData($key)
    {
        if (is_null($this->_enteredData)) {
            $this->_enteredData = Mage::getSingleton('Mage_Wishlist_Model_Session')
                ->getData('sharing_form', true);
        }

        if (!$this->_enteredData || !isset($this->_enteredData[$key])) {
            return null;
        }
        else {
            return $this->escapeHtml($this->_enteredData[$key]);
        }
    }

    /**
     * Retrieve back button url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }

    /**
     * Retrieve number of emails allowed for sharing
     *
     * @return int
     */
    public function getEmailSharingLimit()
    {
        return $this->_wishlistConfig->getSharingEmailLimit();
    }

    /**
     * Retrieve maximum email length allowed for sharing
     *
     * @return int
     */
    public function getTextSharingLimit()
    {
        return $this->_wishlistConfig->getSharingTextLimit();
    }
}
