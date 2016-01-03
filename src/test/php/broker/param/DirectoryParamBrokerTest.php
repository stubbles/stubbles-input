<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker\param;
use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
/**
 * Tests for stubbles\input\broker\param\DirectoryParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class DirectoryParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->markTestIncomplete();
        $this->paramBroker = new DirectoryParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Directory';
    }

    /**
     * returns expected filtered value
     *
     * @return  bool
     */
    protected function expectedValue()
    {
        return __DIR__;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => '/home/user'])
                ),
                equals('/home/user')
        );
    }

    /**
     * @test
     */
    public function considersRelativeWhenBasePathGiven()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest('../'),
                        $this->createRequestAnnotation(
                                ['basePath'      => __DIR__,
                                 'allowRelative' => true
                                ]
                        )
                ),
                equals('../')
        );
    }
}
