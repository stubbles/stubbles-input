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
use net\stubbles\lang\BaseObject;
/**
 * Detector which detects single properties of user agents.
 *
 * Currently it only supports to detect if a user agent is a bot. This detection
 * is limited to the Googlebot, MSNbot, Yahoo! Slurp and the DotBot.
 *
 * @since  1.2.0
 */
class UserAgentDetector extends BaseObject
{
    /**
     * list of known bot user agents
     *
     * @type  array
     */
    protected $botUserAgents = array('google' => '~Googlebot~',
                                     'msnbot' => '~msnbot~',
                                     'slurp'  => '~Slurp~',
                                     'dotbot' => '~DotBot~'
                               );

    /**
     * detects the user agent
     *
     * @param   string  $userAgentString
     * @return  UserAgent
     */
    public function detect($userAgentString)
    {
        return new UserAgent($userAgentString, $this->isBot($userAgentString));
    }

    /**
     * helper method to detect whether a user agent is a bot or not
     *
     * @param   string  $userAgentString
     * @return  bool
     */
    protected function isBot($userAgentString)
    {
        foreach ($this->botUserAgents as $botUserAgent) {
            if (preg_match($botUserAgent, $userAgentString) === 1) {
                return true;
            }
        }

        return false;
    }
}
?>