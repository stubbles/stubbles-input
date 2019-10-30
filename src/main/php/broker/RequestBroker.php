<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker;
use stubbles\input\Request;
use stubbles\input\broker\param\ParamBroker;
use stubbles\sequence\Sequence;

use function stubbles\reflect\annotationsOf;
use function stubbles\reflect\methodsOf;
/**
 * Broker class to transfer values from the request into an object via annotations.
 *
 * @api
 * @Singleton
 */
class RequestBroker
{
    /**
     * list of build in param brokers
     *
     * @type  \stubbles\input\broker\param\ParamBroker[]
     */
    private static $buildInParamBroker;

    /**
     * returns list of build in param brokers
     *
     * @return  \stubbles\input\broker\param\ParamBroker[]
     */
    public static function buildInTypes(): array
    {
        if (null === self::$buildInParamBroker) {
            self::$buildInParamBroker = [
                    'array'          => new param\ArrayParamBroker(),
                    'bool'           => new param\BoolParamBroker(),
                    'customdatespan' => new param\CustomDatespanParamBroker(),
                    'date'           => new param\DateParamBroker(),
                    'datespan'       => new param\DatespanParamBroker(),
                    'day'            => new param\DayParamBroker(),
                    'float'          => new param\FloatParamBroker(),
                    'httpuri'        => new param\HttpUriParamBroker(),
                    'integer'        => new param\IntegerParamBroker(),
                    'json'           => new param\JsonParamBroker(),
                    'mail'           => new param\MailParamBroker(),
                    'month'          => new param\MonthParamBroker(),
                    'oneof'          => new param\OneOfParamBroker(),
                    'password'       => new param\PasswordParamBroker(),
                    'string'         => new param\StringParamBroker(),
                    'secret'         => new param\SecretParamBroker(),
                    'text'           => new param\TextParamBroker(),
                    'week'           => new param\WeekParamBroker(),
                ];
        }

        return self::$buildInParamBroker;
    }

    /**
     * map of param brokers
     *
     * @type  \stubbles\input\broker\ParamBrokers
     */
    private $paramBrokers;

    /**
     * constructor
     *
     * @param   \stubbles\input\broker\param\ParamBroker[]  $paramBrokers  optional
     * @Map(stubbles\input\broker\param\ParamBroker.class)
     */
    public function __construct(array $paramBrokers = [])
    {
        $this->paramBrokers = self::buildInTypes();
        foreach ($paramBrokers as $key => $paramBroker) {
            $this->paramBrokers[strtolower($key)] = $paramBroker;
        }
    }

    /**
     * fills given object with values from request
     *
     * @param   \stubbles\input\Request  $request
     * @param   object                   $object   the object instance to fill with values
     * @param   string                   $group    restrict procurement to given group
     * @return  object
     * @throws  \InvalidArgumentException
     */
    public function procure(Request $request, $object, string $group = null)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('Parameter $object must be an object instance');
        }

        foreach (self::targetMethodsOf($object, $group) as $targetMethod) {
            $targetMethod->invoke(
                    $object,
                    $this->paramBroker($targetMethod->expectedType())
                         ->procure($request, $targetMethod->annotation())
            );
        }

        return $object;
    }

    /**
     * returns param broker for requested type
     *
     * @param   string  $type
     * @return  \stubbles\input\broker\param\ParamBroker
     * @throws  \RuntimeException
     */
    public function paramBroker($type): ParamBroker
    {
        if (isset($this->paramBrokers[$type])) {
            return $this->paramBrokers[$type];
        }

        throw new \RuntimeException('No param broker found for ' . $type);
    }

    /**
     * returns all methods of given instance which are applicable for brokerage
     *
     * @param   object|string|\ReflectionClass  $object
     * @param   string                          $group   optional  restrict list to given group
     * @return  \stubbles\input\broker\TargetMethod[]
     */
    public static function targetMethodsOf($object, string $group = null): Sequence
    {
        return methodsOf($object, \ReflectionMethod::IS_PUBLIC)->filter(
                function(\ReflectionMethod $method) use ($group)
                {
                    if ($method->isStatic() || $method->isConstructor() || $method->isDestructor()) {
                        return false;
                    }

                    if (!annotationsOf($method)->contain('Request')) {
                        return false;
                    }

                    if (empty($group) || annotationsOf($method)->firstNamed('Request')->paramGroup() === $group) {
                        return true;
                    }

                    return false;
                }
        )->map(
                function(\ReflectionMethod $method)
                {
                    return new TargetMethod(
                            $method,
                            annotationsOf($method)->firstNamed('Request')
                    );
                }
        );
    }
}
