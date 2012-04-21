<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker;
use net\stubbles\input\Request;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\reflect\BaseReflectionClass;
use net\stubbles\lang\reflect\ReflectionObject;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker class to transfer values from the request into an object via annotations.
 *
 * @Singleton
 */
class RequestBroker extends BaseObject
{
    /**
     * the matcher to be used for methods and properties
     *
     * @type  RequestBrokerMethodMatcher
     */
    private static $methodMatcher;
    /**
     * map of build in param brokers
     *
     * @type  ParamBroker[]
     */
    private static $buildInParamBroker;
    /**
     * factory to create filters with
     *
     * @type  ParamBroker[]
     */
    private $paramBroker;

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$methodMatcher      = new RequestBrokerMethodMatcher();
        self::$buildInParamBroker = array('Array'          => new param\ArrayParamBroker(),
                                          'Bool'           => new param\BoolParamBroker(),
                                          'CustomDatespan' => new param\CustomDatespanParamBroker(),
                                          'Date'           => new param\DateParamBroker(),
                                          'Day'            => new param\DayParamBroker(),
                                          'Directory'      => new param\DirectoryParamBroker(),
                                          'File'           => new param\FileParamBroker(),
                                          'Float'          => new param\FloatParamBroker(),
                                          'HttpUri'        => new param\HttpUriParamBroker(),
                                          'Integer'        => new param\IntegerParamBroker(),
                                          'Jaon'           => new param\JsonParamBroker(),
                                          'Mail'           => new param\MailParamBroker(),
                                          'Password'       => new param\PasswordParamBroker(),
                                          'String'         => new param\StringParamBroker(),
                                          'Text'           => new param\TextParamBroker(),
                                    );
    }

    /**
     * constructor
     */
    public function __construct()
    {
        $this->paramBroker = self::$buildInParamBroker;
    }

    /**
     * constructor
     *
     * @param   ParamBroker[]  $paramBroker
     * @return  RequestBroker
     * @Inject(optional=true)
     * @Map(net\stubbles\input\broker\param\ParamBroker.class)
     */
    public function setParamBroker(array $paramBroker)
    {
        $this->paramBroker = array_merge(self::$buildInParamBroker,
                                         $paramBroker
                             );
        return $this;
    }

    /**
     * does the real action
     *
     * @param   Request  $request
     * @param   object   $object   the object instance to fill with values
     * @param   string   $group    group of values to filter
     * @throws  IllegalArgumentException
     */
    public function process(Request $request, $object, $group = null)
    {
        if (!is_object($object)) {
            throw new IllegalArgumentException('Parameter $object must be a concrete object instance.');
        }

        $refClass = new ReflectionObject($object);
        foreach ($refClass->getMethodsByMatcher(self::$methodMatcher) as $refMethod) {
            $requestAnnotation = $refMethod->getAnnotation('Request');
            if ($this->isNotInGroup($group, $requestAnnotation)) {
                continue;
            }

            $value = $this->handle($request, $requestAnnotation);
            if (null !== $value) {
                $refMethod->invoke($object, $value);
            }
        }
    }

    /**
     * checks whether the annotation belongs to the given group
     *
     * @param   string      $group
     * @param   Annotation  $requestAnnotation
     * @return  bool
     */
    private function isNotInGroup($group, Annotation $requestAnnotation)
    {
        if (empty($group)) {
            return false;
        }

        return $requestAnnotation->getGroup() !== $group;
    }

    /**
     * reads param and returns its name and value
     *
     * @param   Request     $request
     * @param   Annotation  $requestAnnotation
     * @return  mixed
     * @throws  RuntimeException
     */
    private function handle(Request $request, Annotation $requestAnnotation)
    {
        if (isset($this->paramBroker[$requestAnnotation->getAnnotationName()])) {
            return $this->paramBroker[$requestAnnotation->getAnnotationName()]->handle($request, $requestAnnotation);
        }

        throw new RuntimeException('No param broker found for ' . $requestAnnotation->getAnnotationName());
    }
}
RequestBroker::__static();
?>