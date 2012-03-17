<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\web\useragent;
use net\stubbles\input\web\WebRequest;
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\BaseObject;
/**
 * Factory to create user agent instances.
 *
 * @since  1.2.0
 */
class UserAgentProvider extends BaseObject implements InjectionProvider
{
    /**
     * request instance to be used
     *
     * @type  WebRequest
     */
    protected $request;
    /**
     * filter to be used to detect the user agent
     *
     * @type  UserAgentFilter
     */
    protected $userAgentFilter;

    /**
     * constructor
     *
     * @param  WebRequest       $request
     * @param  UserAgentFilter  $userAgentFilter
     * @Inject
     */
    public function __construct(WebRequest $request, UserAgentFilter $userAgentFilter)
    {
        $this->request         = $request;
        $this->userAgentFilter = $userAgentFilter;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  UserAgent
     */
    public function get($name = null)
    {
        return $this->request->filterHeader('HTTP_USER_AGENT')->applyFilter($this->userAgentFilter);
    }
}
?>