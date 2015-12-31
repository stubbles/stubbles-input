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
use stubbles\date\span\Day;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
/**
 * Tests for stubbles\input\broker\param\DatespanParamBroker.
 *
 * @group  broker
 * @group  broker_param
 * @since  4.3.0
 */
class DatespanParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new DatespanParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Datespan';
    }

    /**
     * returns expected filtered value
     *
     * @return  \stubbles\date\span\Datespan
     */
    protected function expectedValue()
    {
        return new Day('2012-04-21');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => '2012-04-21'])
                ),
                equals(new Day('2012-04-21'))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['required' => true])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBeforeMinStartDate()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('yesterday'),
                        $this->createRequestAnnotation(['minStartDate' => 'today'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxStartDate()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('today'),
                        $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest('today'),
                        $this->createRequestAnnotation(
                                ['minStartDate' => 'yesterday',
                                 'maxEndDate'   => 'tomorrow'
                                ]
                        )
                ),
                equals(new Day('today'))
        );
    }
}
