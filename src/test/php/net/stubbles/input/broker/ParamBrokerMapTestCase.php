<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker;
/**
 * Tests for net\stubbles\input\broker\ParamBrokerMap.
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
        $this->assertTrue($this->paramBrokerMap->getClass()->hasAnnotation('Singleton'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetParamBrokersMethod()
    {
        $setParamBrokers = $this->paramBrokerMap->getClass()->getMethod('setParamBrokers');
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
            $defaultBroker[] = array($name, $paramBroker->getClassName());
        }

        return $defaultBroker;
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
     * @expectedException  net\stubbles\lang\exception\RuntimeException
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
        $mockParamBroker = $this->getMock('net\\stubbles\\input\\broker\\param\\ParamBroker');
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
        $mockParamBroker = $this->getMock('net\\stubbles\\input\\broker\\param\\ParamBroker');
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
        $mockParamBroker = $this->getMock('net\\stubbles\\input\\broker\\param\\ParamBroker');
        $this->assertSame($mockParamBroker,
                          $this->paramBrokerMap->setParamBrokers(array('String' => $mockParamBroker))
                                               ->getBroker('String')
        );
    }
}
?>