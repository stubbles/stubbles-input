<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\date\Date;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\DateParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class DateParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new DateParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Date';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): Date
    {
        return new Date('2012-04-21 00:00:00+02:00');
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['default' => '2012-04-21'])
            ),
            equals(new Date('2012-04-21'))
        );
    }

    #[Test]
    public function returnsNullIfParamNotSetAndRequired(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['required' => true])
            )
        );
    }

    #[Test]
    public function returnsNullIfBeforeMinDate(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('yesterday'),
                $this->createRequestAnnotation(['minDate' => 'today'])
            )
        );
    }

    #[Test]
    public function returnsNullIfAfterMaxDate(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('today'),
                $this->createRequestAnnotation(['maxDate' => 'yesterday'])
            )
        );
    }

    #[Test]
    public function returnsValueIfInRange(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('today'),
                $this->createRequestAnnotation(
                    ['minDate' => 'yesterday', 'maxDate'  => 'tomorrow']
                )
            ),
            equals(new Date('today'))
        );
    }
}
