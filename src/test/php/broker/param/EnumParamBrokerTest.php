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
use stubbles\lang\Enum;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
class ExampleEnum extends Enum
{
    public static $FOO;

    public static $BAR;

    public static function __static()
    {
        self::$FOO = new self('foo', 303);
        self::$BAR = new self('bar', 909);
    }

    public function __toString()
    {
        return $this->name;
    }
}
ExampleEnum::__static();
/**
 * Tests for stubbles\input\broker\param\EnumParamBroker.
 *
 * @group  broker
 * @group  broker_param
 * @since  5.0.0
 */
class EnumParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new EnumParamBroker();
    }

    /**
     * creates request annotation
     *
     * @param   array  $values
     * @return  Annotation
     */
    protected function createRequestAnnotation(array $values = [])
    {
        $values['enumClass'] = ExampleEnum::class;
        return parent::createRequestAnnotation($values);
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Enum';
    }

    /**
     * returns expected filtered value
     *
     * @return  bool
     */
    protected function expectedValue()
    {
        return ExampleEnum::$FOO;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertSame(
                ExampleEnum::$BAR,
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => ExampleEnum::class . '::$BAR'])
                )
        );
    }
}
