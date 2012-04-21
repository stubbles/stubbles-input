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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\RuntimeException;
/**
 * Map which contains all single parameter brokers.
 *
 * @Singleton
 */
class ParamBrokerMap extends BaseObject
{
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
     * sets map of param brokers
     *
     * @param   ParamBroker[]  $paramBroker
     * @return  ParamBrokerMap
     * @Inject(optional=true)
     * @Map(net\stubbles\input\broker\param\ParamBroker.class)
     */
    public function setParamBrokers(array $paramBroker)
    {
        $this->paramBroker = array_merge(self::$buildInParamBroker,
                                         $paramBroker
                             );
        return $this;
    }


    /**
     * retrieves param broker for given key
     *
     * @param   string  $key
     * @return  param\ParamBroker
     * @throws  RuntimeException
     */
    public function getBroker($key)
    {
        if (isset($this->paramBroker[$key])) {
            return $this->paramBroker[$key];
        }

        throw new RuntimeException('No param broker found for ' . $key);
    }
}
ParamBrokerMap::__static();
?>