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
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Tests for stubbles\input\broker\ParamBrokerMap.
 *
 * @group  broker
 * @group  broker_core
 */
class ParamBrokersTest extends \PHPUnit_Framework_TestCase
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
        $this->paramBrokerMap = new ParamBrokers();
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue(
                lang\reflect($this->paramBrokerMap)->hasAnnotation('Singleton')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnAddParamBrokersMethod()
    {
        $setParamBrokers = lang\reflect($this->paramBrokerMap, 'addParamBrokers');
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
        foreach (ParamBrokers::buildIn() as $name => $paramBroker) {
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
                ['SecureString', 'stubbles\input\broker\param\SecureStringParamBroker'],
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
                                $this->paramBrokerMap->paramBroker($key)
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
                                $this->paramBrokerMap->paramBroker(strtolower($key))
        );
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function procureUnknownTypeThrowsRuntimeException()
    {
        $this->paramBrokerMap->procure(
                $this->getMock('stubbles\input\Request'),
                new Annotation('doesNotExist', 'foo')
        );
    }

    /**
     * @test
     * @dataProvider  getDefaultBrokerList
     */
    public function addingBrokersDoesNotOverrideDefaultBrokers($key, $brokerClass)
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertInstanceOf($brokerClass,
                                $this->paramBrokerMap->addParamBrokers(['Mock' => $mockParamBroker])
                                                     ->paramBroker($key)
        );
    }

    /**
     * @test
     */
    public function returnsAddedBroker()
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertSame($mockParamBroker,
                          $this->paramBrokerMap->addParamBrokers(['Mock' => $mockParamBroker])
                                               ->paramBroker('Mock')
        );
    }

    /**
     * @test
     */
    public function canOverwriteDefaultBroker()
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertSame($mockParamBroker,
                          $this->paramBrokerMap->addParamBrokers(['string' => $mockParamBroker])
                                               ->paramBroker('string')
        );
    }
}
