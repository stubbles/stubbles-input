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
 * Provides bot and cookie acceptance detection. Bot detection however is
 * limited to the Googlebot, MSNbot, Yahoo! Slurp and the DotBot.
 *
 * Cookie acceptance detection works by checking whether the user agent sent any
 * cookies with the request. If the user agent didn't send any cookies we assume
 * it doesn't accept cookies - which must not be neccessarily true, it might
 * just be that the user agent didn't receive any cookie before.
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
    private $request;
    /**
     * list of known bot user agents
     *
     * @type  array
     */
    private $botUserAgents = array('google' => '~Googlebot~',
                                   'msnbot' => '~msnbot~',
                                   'slurp'  => '~Slurp~',
                                   'dotbot' => '~DotBot~'
                             );

    /**
     * constructor
     *
     * @param  WebRequest  $request
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
     * @return  UserAgent
     */
    public function get($name = null)
    {
        $userAgentString = $this->readUserAgentString();
        return new UserAgent($userAgentString,
                             $this->isBot($userAgentString),
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
     * helper method to detect whether a user agent is a bot or not
     *
     * @param   string  $userAgentString
     * @return  bool
     */
    private function isBot($userAgentString)
    {
        foreach ($this->botUserAgents as $botUserAgent) {
            if (preg_match($botUserAgent, $userAgentString) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * checks whether user agent accepts cookies
     *
     * @return  bool
     */
    private function acceptsCookies()
    {
        return count($this->request->getCookieNames()) > 0;
    }
}
?>