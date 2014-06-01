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
        $defaultBroker = [];
        foreach (ParamBrokerMap::getBuildInParamBroker() as $name => $paramBroker) {
            $defaultBroker[] = [$name, get_class($paramBroker)];
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
        return [['Array', 'stubbles\input\broker\param\ArrayParamBroker'],
                ['Bool', 'stubbles\input\broker\param\BoolParamBroker'],
                ['CustomDatespan', 'stubbles\input\broker\param\CustomDatespanParamBroker'],
                ['Date', 'stubbles\input\broker\param\DateParamBroker'],
                ['Day', 'stubbles\input\broker\param\DayParamBroker'],
                ['Directory', 'stubbles\input\broker\param\DirectoryParamBroker'],
                ['File', 'stubbles\input\broker\param\FileParamBroker'],
                ['Float', 'stubbles\input\broker\param\FloatParamBroker'],
                ['HttpUri', 'stubbles\input\broker\param\HttpUriParamBroker'],
                ['Integer', 'stubbles\input\broker\param\IntegerParamBroker'],
                ['Json', 'stubbles\input\broker\param\JsonParamBroker'],
                ['Mail', 'stubbles\input\broker\param\MailParamBroker'],
                ['OneOf', 'stubbles\input\broker\param\OneOfParamBroker'],
                ['Password', 'stubbles\input\broker\param\PasswordParamBroker'],
                ['String', 'stubbles\input\broker\param\StringParamBroker'],
                ['Text', 'stubbles\input\broker\param\TextParamBroker'],
         ];
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
                                $this->paramBrokerMap->setParamBrokers(['Mock' => $mockParamBroker])
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
                          $this->paramBrokerMap->setParamBrokers(['Mock' => $mockParamBroker])
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
                          $this->paramBrokerMap->setParamBrokers(['string' => $mockParamBroker])
                                               ->getBroker('string')
        );
    }
}
