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
use stubbles\input\Request;
use stubbles\lang;
use stubbles\lang\reflect\BaseReflectionClass;
/**
 * Broker class to transfer values from the request into an object via annotations.
 *
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
    public static function buildInTypes()
    {
        if (null === self::$buildInParamBroker) {
            self::$buildInParamBroker = ['array'          => new param\ArrayParamBroker(),
                                         'bool'           => new param\BoolParamBroker(),
                                         'customdatespan' => new param\CustomDatespanParamBroker(),
                                         'date'           => new param\DateParamBroker(),
                                         'day'            => new param\DayParamBroker(),
                                         'directory'      => new param\DirectoryParamBroker(),
                                         'file'           => new param\FileParamBroker(),
                                         'float'          => new param\FloatParamBroker(),
                                         'httpuri'        => new param\HttpUriParamBroker(),
                                         'integer'        => new param\IntegerParamBroker(),
                                         'json'           => new param\JsonParamBroker(),
                                         'mail'           => new param\MailParamBroker(),
                                         'oneof'          => new param\OneOfParamBroker(),
                                         'password'       => new param\PasswordParamBroker(),
                                         'string'         => new param\StringParamBroker(),
                                         'securestring'   => new param\SecureStringParamBroker(),
                                         'text'           => new param\TextParamBroker(),
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
     */
    public function __construct()
    {
        $this->paramBrokers = self::buildInTypes();
    }

    /**
     * adds map of param brokers
     *
     * @param   \stubbles\input\broker\param\ParamBroker[]  $paramBrokers
     * @return  \stubbles\input\broker\RequestBroker
     * @Inject(optional=true)
     * @Map(stubbles\input\broker\param\ParamBroker.class)
     */
    public function addParamBrokers(array $paramBrokers)
    {
        foreach ($paramBrokers as $key => $paramBroker) {
            $this->paramBrokers[strtolower($key)] = $paramBroker;
        }

        return $this;
    }

    /**
     * fills given object with values from request
     *
     * @param   \stubbles\input\Request  $request
     * @param   object                   $object   the object instance to fill with values
     * @param   string                   $group    restrict procurement to given group
     * @throws  \InvalidArgumentException
     */
    public function procure(Request $request, $object, $group = null)
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
    }

    /**
     * returns param broker for requested type
     *
     * @param   string  $type
     * @return  \stubbles\input\broker\param\ParamBroker
     * @throws  \RuntimeException
     */
    public function paramBroker($type)
    {
        if (isset($this->paramBrokers[$type])) {
            return $this->paramBrokers[$type];
        }

        throw new \RuntimeException('No param broker found for ' . $type);
    }

    /**
     * returns all methods of given instance which are applicable for brokerage
     *
     * @param   object|string  $object
     * @param   string         $group   restrict list to given group
     * @return  \stubbles\input\broker\TargetMethod[]
     * @throws  \InvalidArgumentException
     */
    public static function targetMethodsOf($object, $group = null)
    {
        if (!is_object($object) && !is_string($object) && !($object instanceof BaseReflectionClass)) {
            throw new \InvalidArgumentException('Parameter $object must be an object instance, a class name or an instance of stubbles\lang\reflect\BaseReflectionClass');
        }

        $class = $object instanceof BaseReflectionClass ? $object : lang\reflect($object);
        $brokeredParams = [];
        foreach ($class->getMethodsByMatcher(new ApplicableMethods()) as $method) {
            $annotation = $method->annotation('Request');
            if (empty($group) || $annotation->paramGroup() === $group) {
                $brokeredParams[] = new TargetMethod($method, $annotation);
            }
        }

        return $brokeredParams;
    }
}
