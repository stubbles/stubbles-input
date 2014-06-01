<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker;
use stubbles\lang;
/**
 * Tests for stubbles\input\broker\ParamBrokerMap.
 *
 * @group  broker
 * @group  broker_core
 */
class ParamBrokerMapTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ParamBrokerMap
     */
    private $paramBrokerMap;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBrokerMap = new ParamBrokerMap();
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue(lang\reflect($this->paramBrokerMap)->hasAnnotation('Singleton'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetParamBrokersMethod()
    {
        $setParamBrokers = lang\reflect($this->paramBrokerMap, 'setParamBrokers');
        $this->assertTrue($setParamBrokers->hasAnnotation('Inject'));
        $this->assertTrue($setParamBrokers->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setParamBrokers->hasAnnotation('Map'));
    }

    /**
     * returns default broker list
     *
     * @return  array
     */
    public function getDefaultBrokerList()
    {
        $defaultBroker = array();
        foreach (ParamBrokerMap::getBuildInParamBroker() as $name => $paramBroker) {
            $defaultBroker[] = array($name, get_class($paramBroker));
        }

        return array_merge($defaultBroker, $this->getBcDefaultBrokerList());
    }

    /**
     * returns a list of default brokers for backward compatibility with old keys
     *
     * @return  array
     * @since   2.3.3
     */
    private function getBcDefaultBrokerList()
    {
        return array(array('Array', 'stubbles\input\broker\param\ArrayParamBroker'),
                     array('Bool', 'stubbles\input\broker\param\BoolParamBroker'),
                     array('CustomDatespan', 'stubbles\input\broker\param\CustomDatespanParamBroker'),
                     array('Date', 'stubbles\input\broker\param\DateParamBroker'),
                     array('Day', 'stubbles\input\broker\param\DayParamBroker'),
                     array('Directory', 'stubbles\input\broker\param\DirectoryParamBroker'),
                     array('File', 'stubbles\input\broker\param\FileParamBroker'),
                     array('Float', 'stubbles\input\broker\param\FloatParamBroker'),
                     array('HttpUri', 'stubbles\input\broker\param\HttpUriParamBroker'),
                     array('Integer', 'stubbles\input\broker\param\IntegerParamBroker'),
                     array('Json', 'stubbles\input\broker\param\JsonParamBroker'),
                     array('Mail', 'stubbles\input\broker\param\MailParamBroker'),
                     array('OneOf', 'stubbles\input\broker\param\OneOfParamBroker'),
                     array('Password', 'stubbles\input\broker\param\PasswordParamBroker'),
                     array('String', 'stubbles\input\broker\param\StringParamBroker'),
                     array('Text', 'stubbles\input\broker\param\TextParamBroker'),
         );
    }

    /**
     * @test
     * @dataProvider  getDefaultBrokerList
     */
    public function returnsBroker($key, $brokerClass)
    {
        $this->assertInstanceOf($brokerClass,
                                $this->paramBrokerMap->getBroker($key)
        );
    }

    /**
     * @test
     * @dataProvider  getDefaultBrokerList
     * @since  2.3.3
     * @group  issue_45
     */
    public function returnsBrokerWithLowerCaseKey($key, $brokerClass)
    {
        $this->assertInstanceOf($brokerClass,
                                $this->paramBrokerMap->getBroker(strtolower($key))
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\RuntimeException
     */
    public function getUnknownBrokerThrowsRuntimeException()
    {
        $this->paramBrokerMap->getBroker('doesNotExist');
    }

    /**
     * @test
     * @dataProvider  getDefaultBrokerList
     */
    public function settingBrokersDoesNotOverrideDefaultBrokers($key, $brokerClass)
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertInstanceOf($brokerClass,
                                $this->paramBrokerMap->setParamBrokers(array('Mock' => $mockParamBroker))
                                                     ->getBroker($key)
        );
    }

    /**
     * @test
     */
    public function returnsAddedBroker()
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertSame($mockParamBroker,
                          $this->paramBrokerMap->setParamBrokers(array('Mock' => $mockParamBroker))
                                               ->getBroker('Mock')
        );
    }

    /**
     * @test
     */
    public function canOverwriteDefaultBroker()
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertSame($mockParamBroker,
                          $this->paramBrokerMap->setParamBrokers(array('string' => $mockParamBroker))
                                               ->getBroker('string')
        );
    }
}
