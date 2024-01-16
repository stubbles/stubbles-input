<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\input\ValueReader;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\ArrayParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class ArrayParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new ArrayParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Array';
    }

    /**
     * returns expected filtered value
     *
     * @return  string[]
     */
    protected function expectedValue(): array
    {
        return ['foo', 'bar'];
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['default' => 'foo|bar'])
            ),
            equals($this->expectedValue())
        );
    }

    #[Test]
    public function returnsValueWithDifferentSeparator(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('foo|bar'),
                $this->createRequestAnnotation(['separator' => '|'])
            ),
            equals($this->expectedValue())
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
    public function returnsEmptyArrayForEmptyValue(): void
    {
        assertEmptyArray(
            $this->paramBroker->procure(
                $this->createRequest(''),
                $this->createRequestAnnotation([])
            )
        );
    }

    #[Test]
    public function usesParamAsDefaultSource(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('foo, bar'),
                $this->createRequestAnnotation([])
            ),
            equals($this->expectedValue())
        );
    }

    #[Test]
    public function usesParamAsSource(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('foo, bar'),
                $this->createRequestAnnotation(['source' => 'param'])
            ),
            equals($this->expectedValue())
        );
    }

    #[Test]
    public function canUseHeaderAsSourceForWebRequest(): void
    {
        $request = NewInstance::of(WebRequest::class)->returns([
            'readHeader' => ValueReader::forValue('foo, bar')
        ]);
        assertThat(
            $this->paramBroker->procure(
                $request,
                $this->createRequestAnnotation(['source' => 'header'])
            ),
            equals($this->expectedValue())
        );
    }

    #[Test]
    public function canUseCookieAsSourceForWebRequest(): void
    {
        $request = NewInstance::of(WebRequest::class)->returns([
            'readCookie' => ValueReader::forValue('foo, bar')
        ]);
        assertThat(
            $this->paramBroker->procure(
                $request,
                $this->createRequestAnnotation(['source' => 'cookie'])
            ),
            equals($this->expectedValue())
        );
    }
}
