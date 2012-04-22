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
        return array(array('Array', 'net\\stubbles\\input\\broker\\param\\ArrayParamBroker'),
                     array('Bool', 'net\\stubbles\\input\\broker\\param\\BoolParamBroker'),
                     array('CustomDatespan', 'net\\stubbles\\input\\broker\\param\\CustomDatespanParamBroker'),
                     array('Date', 'net\\stubbles\\input\\broker\\param\\DateParamBroker'),
                     array('Day', 'net\\stubbles\\input\\broker\\param\\DayParamBroker'),
                     array('Directory', 'net\\stubbles\\input\\broker\\param\\DirectoryParamBroker'),
                     array('File', 'net\\stubbles\\input\\broker\\param\\FileParamBroker'),
                     array('Float', 'net\\stubbles\\input\\broker\\param\\FloatParamBroker'),
                     array('HttpUri', 'net\\stubbles\\input\\broker\\param\\HttpUriParamBroker'),
                     array('Integer', 'net\\stubbles\\input\\broker\\param\\IntegerParamBroker'),
                     array('Jaon', 'net\\stubbles\\input\\broker\\param\\JsonParamBroker'),
                     array('Mail', 'net\\stubbles\\input\\broker\\param\\MailParamBroker'),
                     array('Password', 'net\\stubbles\\input\\broker\\param\\PasswordParamBroker'),
                     array('String', 'net\\stubbles\\input\\broker\\param\\StringParamBroker'),
                     array('Text', 'net\\stubbles\\input\\broker\\param\\TextParamBroker'),
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