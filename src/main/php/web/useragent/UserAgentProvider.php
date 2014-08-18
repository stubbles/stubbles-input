<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\web\useragent;
use stubbles\input\web\WebRequest;
use stubbles\ioc\InjectionProvider;
/**
 * Factory to create user agent instances.
 *
 * Provides bot and cookie acceptance detection. Bot detection however is
 * limited to the Googlebot, MSNbot, Yahoo! Slurp and the DotBot.
 *
 * Cookie acceptance detection works by checking whether the user agent sent any
 * cookies with the request. If the user agent didn't send any cookies we assume
 * it doesn't accept cookies - which must not be neccessarily true, it might
 * just be that the user agent didn't receive any cookie before.
 *
 * @since  1.2.0
 * @deprecated  since 4.1.0, use $request->userAgent() instead, will be removed with 5.0.0
 */
class UserAgentProvider implements InjectionProvider
{
    /**
     * request instance to be used
     *
     * @type  \stubbles\input\web\WebRequest
     */
    private $request;

    /**
     * constructor
     *
     * @param  \stubbles\input\web\WebRequest  $request
     * @Inject
     */
    public function __construct(WebRequest $request)
    {
        $this->request = $request;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  \stubbles\input\web\useragent\UserAgent
     */
    public function get($name = null)
    {
        return new UserAgent($this->readUserAgentString(),
                             $this->acceptsCookies()
        );
    }

    /**
     * reads user agent string
     *
     * @return  string
     */
    private function readUserAgentString()
    {
        return $this->request->readHeader('HTTP_USER_AGENT')->asString();
    }

    /**
     * checks whether user agent accepts cookies
     *
     * @return  bool
     */
    private function acceptsCookies()
    {
        return count($this->request->cookieNames()) > 0;
    }
}
