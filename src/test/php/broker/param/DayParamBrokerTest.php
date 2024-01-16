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
use stubbles\date\span\Day;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\DayParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class DayParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new DayParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Day';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): Day
    {
        return new Day('2012-04-21');
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['default' => '2012-04-21'])
            ),
            equals(new Day('2012-04-21'))
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
    public function returnsNullIfBeforeMinStartDate(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('yesterday'),
                $this->createRequestAnnotation(['minStartDate' => 'today'])
            )
        );
    }

    #[Test]
    public function returnsNullIfAfterMaxStartDate(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('today'),
                $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
            )
        );
    }

    #[Test]
    public function returnsValueIfInRange(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('today'),
                $this->createRequestAnnotation([
                    'minStartDate' => 'yesterday',
                    'maxEndDate'   => 'tomorrow'
                ])
            ),
            equals(new Day('today'))
        );
    }
}
