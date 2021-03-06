<?php
/**
 * Test Array_Converter functionality
 *
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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Varien_Convert_ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider mappingDataProvider
     */
    public function testMappingData($data, $expectedData)
    {
        $converter = new Varien_Convert_Object();
        $convertedData = $converter->convertDataToArray($data);
        $this->assertEquals($expectedData, $convertedData);
    }

    public function testMappingDataCycleDetected()
    {
        $objectA = new Varien_Object(array('keyA' => 'valueA'));
        $objectB = new Varien_Object(array('keyB' => 'valueB', 'object' => $objectA));
        $objectA->setObject($objectB);
        $converter = new Varien_Convert_Object();
        $expectedData = array(
            array('keyA'   => 'valueA',
                  'object' => array('keyB'   => 'valueB',
                                    'object' => Varien_Convert_Object::CYCLE_DETECTED_MARK)));
        $this->assertEquals($expectedData, $converter->convertDataToArray(array($objectA)));
    }

    public function mappingDataProvider()
    {
        return array(
            array(
                array('object' => new Varien_Object(array('keyA' => 'valueA'))),
                array('object' => array('keyA' => 'valueA'))
            ),
            array(
                array('objectA' => new Varien_Object(array('keyA' => 'valueA')),
                      'objectB' => new Varien_Object(array(
                                                          'keyB' => new Varien_Object(array(
                                                                                           'keyC'     => 'valueC',
                                                                                           'password' => 'qa123123'))
                                                     ))),
                array('objectA' => array('keyA' => 'valueA'),
                      'objectB' => array(
                          'keyB' => array(
                              'keyC'     => 'valueC',
                              'password' => 'qa123123'))) // We no longer redact as part of conversion
            ),
            array(
                array(),
                array()
            ),
            array(
                array(555888, 'string'    => "Some text",
                      'not_varien_object' => new Varien_Convert_ObjectTest_SimpleClass(
                          'private', 'protected', 'public'
                      )),
                array(555888, 'string' => "Some text", 'not_varien_object' => array(
                    ' Varien_Convert_ObjectTest_SimpleClass _privateField' => 'private',
                    ' * _protectedField'         => 'protected',
                    'publicField'                => 'public',
                )),
            ),
            array(
                array(
                    array(
                        'some_object' => new Varien_Object(
                            array(
                                 'keyA' => array(
                                     new Varien_Object(
                                         array(
                                              'sub_key' => 'sub_value'
                                         )
                                     )
                                 )
                            )
                        )
                    )
                ),
                array(
                    array(
                        'some_object' => array(
                            'keyA' => array(
                                array(
                                    'sub_key' => 'sub_value'
                                )
                            )
                        )
                    )
                ),
            ),
        );
    }
}

class Varien_Convert_ObjectTest_SimpleClass
{
    public $publicField;

    protected $_protectedField;

    private $_privateField;

    public function __construct($private, $protected, $public)
    {
        $this->_privateField = $private;
        $this->_protectedField = $protected;
        $this->publicField = $public;
    }

    public function getPrivateField()
    {
        return $this->_privateField;
    }
}