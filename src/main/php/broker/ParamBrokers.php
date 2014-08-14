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
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Map which contains all single parameter brokers.
 *
 * @Singleton
 * @internal
 */
class ParamBrokers
{
    /**
     * list of build in param brokers
     *
     * @type  \stubbles\input\broker\param\ParamBroker[]
     */
    private static $buildInParamBroker;
    /**
     * list of registered param brokers
     *
     * @type  \stubbles\input\broker\param\ParamBroker[]
     */
    private $paramBroker;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->paramBroker = self::buildIn();
    }

    /**
     * adds map of param brokers
     *
     * @param   \stubbles\input\broker\param\ParamBroker[]  $paramBrokers
     * @return  \stubbles\input\broker\param\ParamBrokerMap
     * @Inject(optional=true)
     * @Map(stubbles\input\broker\param\ParamBroker.class)
     */
    public function addParamBrokers(array $paramBrokers)
    {
        foreach ($paramBrokers as $key => $paramBroker) {
            $this->paramBroker[strtolower($key)] = $paramBroker;
        }

        return $this;
    }

    /**
     * retrieves value defined by annotation from request
     *
     * @param   \stubbles\input\Request                       $request     instance to handle value with
     * @param   \stubbles\lang\reflect\annotation\Annotation  $annotation  annotation which contains request param metadata
     * @return  mixed
     */
    public function procure(Request $request, Annotation $annotation)
    {
        return $this->paramBroker($annotation->getAnnotationName())
                    ->procure($request, $annotation);
    }

    /**
     * retrieves param broker for given key
     *
     * @param   string  $key
     * @return  \stubbles\input\broker\param\ParamBroker
     * @throws  \RuntimeException
     */
    public function paramBroker($key)
    {
        if (isset($this->paramBroker[strtolower($key)])) {
            return $this->paramBroker[strtolower($key)];
        }

        throw new \RuntimeException('No param broker found for ' . $key);
    }

    /**
     * returns list of build in param brokers
     *
     * @return  \stubbles\input\broker\param\ParamBroker[]
     */
    public static function buildIn()
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
}
