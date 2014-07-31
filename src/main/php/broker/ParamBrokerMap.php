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
use stubbles\lang\exception\RuntimeException;
/**
 * Map which contains all single parameter brokers.
 *
 * @Singleton
 */
class ParamBrokerMap
{
    /**
     * list of build in param brokers
     *
     * @type  \stubbles\input\param\broker\ParamBroker[]
     */
    private static $buildInParamBroker;
    /**
     * list of registered param brokers
     *
     * @type  \stubbles\input\param\broker\ParamBroker[]
     */
    private $paramBroker;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->paramBroker = self::getBuildInParamBroker();
    }

    /**
     * sets map of param brokers
     *
     * @param   \stubbles\input\param\broker\ParamBroker[]  $paramBrokers
     * @return  \stubbles\input\param\ParamBrokerMap
     * @Inject(optional=true)
     * @Map(stubbles\input\broker\param\ParamBroker.class)
     */
    public function setParamBrokers(array $paramBrokers)
    {
        foreach ($paramBrokers as $key => $paramBroker) {
            $this->paramBroker[strtolower($key)] = $paramBroker;
        }

        return $this;
    }

    /**
     * retrieves param broker for given key
     *
     * @param   string  $key
     * @return  \stubbles\input\param\broker\ParamBroker
     * @throws  \stubbles\lang\exception\RuntimeException
     */
    public function getBroker($key)
    {
        if (isset($this->paramBroker[strtolower($key)])) {
            return $this->paramBroker[strtolower($key)];
        }

        throw new RuntimeException('No param broker found for ' . $key);
    }

    /**
     * returns list of build in param brokers
     *
     * @return  \stubbles\input\param\broker\ParamBroker[]
     */
    public static function getBuildInParamBroker()
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
