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
use stdClass;
use stubbles\input\ValueReader;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\JsonParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class JsonParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    private const REQUEST_PARAM_VALUE = '{"method":"add","params":[1,2],"id":1}';

    protected function setUp(): void
    {
        $this->paramBroker = new JsonParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Json';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): stdClass
    {
        $phpJsonObj = new stdClass();
        $phpJsonObj->method = 'add';
        $phpJsonObj->params = [1, 2];
        $phpJsonObj->id = 1;
        return $phpJsonObj;
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(
                    ['default' => self::REQUEST_PARAM_VALUE]
                )
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
    public function returnsNullForInvalidJson(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('{invalid'),
                $this->createRequestAnnotation([])
            )
        );
    }

    #[Test]
    public function usesParamAsDefaultSource(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(self::REQUEST_PARAM_VALUE),
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
                $this->createRequest(self::REQUEST_PARAM_VALUE),
                $this->createRequestAnnotation(['source' => 'param'])
            ),
            equals($this->expectedValue())
        );
    }

    #[Test]
    public function canUseHeaderAsSourceForWebRequest(): void
    {
        $request = NewInstance::of(WebRequest::class)->returns([
            'readHeader' => ValueReader::forValue(self::REQUEST_PARAM_VALUE)
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
            'readCookie' => ValueReader::forValue(self::REQUEST_PARAM_VALUE)
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
