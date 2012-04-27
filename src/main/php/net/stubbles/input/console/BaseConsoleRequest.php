<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\console;
use net\stubbles\input\AbstractRequest;
use net\stubbles\input\Param;
use net\stubbles\input\ParamErrors;
use net\stubbles\input\Params;
use net\stubbles\input\ValueReader;
use net\stubbles\input\ValueValidator;
/**
 * Request implementation for command line.
 *
 * @api
 * @since  2.0.0
 */
class BaseConsoleRequest extends AbstractRequest implements ConsoleRequest
{
    /**
     * list of environment variables
     *
     * @type  Params
     */
    private $env;

    /**
     * constructor
     *
     * @param  array  $params
     * @param  array  $env
     * @Inject
     * @Named('argv')
     */
    public function __construct(array $params, array $env)
    {
        parent::__construct(new Params($params));
        $this->env = new Params($env);
    }

    /**
     * creates an instance from raw data
     *
     * Will use $_SERVER['argv'] for params and $_SERVER for env.
     *
     * @api
     * @return  ConsoleRequest
     */
    public static function fromRawSource()
    {
        return new self($_SERVER['argv'], $_SERVER);
    }

    /**
     * returns the request method
     *
     * @return  string
     */
    public function getMethod()
    {
        return 'cli';
    }

    /**
     * return an array of all environment names registered in this request
     *
     * @return  string[]
     */
    public function getEnvNames()
    {
        return $this->env->getNames();
    }

    /**
     * returns list of errors for environment parameters
     *
     * @return  ParamErrors
     */
    public function envErrors()
    {
        return $this->env->errors();
    }

    /**
     * checks whether a request param is set
     *
     * @param   string  $envName
     * @return  bool
     */
    public function hasEnv($envName)
    {
        return $this->env->has($envName);
    }

    /**
     * checks whether a request value from parameters is valid or not
     *
     * @param   string  $envName  name of environment value
     * @return  ValueValidator
     */
    public function validateEnv($envName)
    {
        return new ValueValidator($this->env->get($envName));
    }

    /**
     * returns request value from params for validation
     *
     * @param   string  $envName  name of environment value
     * @return  ValueFilter
     */
    public function readEnv($envName)
    {
        return new ValueReader($this->env->errors(),
                               $this->env->get($envName)
        );
    }
}
?>