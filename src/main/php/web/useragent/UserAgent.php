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
use stubbles\lang;
/**
 * Value object for user agents.
 *
 * @since  1.2.0
 * @XmlTag(tagName='userAgent')
 * @ProvidedBy(stubbles\input\web\useragent\UserAgentProvider.class)
 */
class UserAgent
{
    /**
     * name of user agent
     *
     * @type  string
     */
    private $name;
    /**
     * whether user agent is a bot or not
     *
     * @type  bool
     */
    private $isBot = null;
    /**
     * list of known bot user agents
     *
     * @type  array
     */
    private $botUserAgents = ['google' => '~Googlebot~',
                              'msnbot' => '~msnbot~',
                              'slurp'  => '~Slurp~',
                              'dotbot' => '~DotBot~'
                             ];
    /**
     * whether user agent accepts cookies or not
     *
     * @type  bool
     */
    private $acceptsCookies;

    /**
     * constructor
     *
     * @param  string    $name            name of user agent
     * @param  bool      $acceptsCookies  whether user agent accepts cookies or not
     * @param  string[]  $botUserAgents   optional  additional map of bot user agent recognitions
     */
    public function __construct($name, $acceptsCookies, $botUserAgents = [])
    {
        $this->name           = $name;
        $this->acceptsCookies = $acceptsCookies;
        $this->botUserAgents  = array_merge($this->botUserAgents, $botUserAgents);
    }

    /**
     * returns name of user agent
     *
     * @XmlAttribute(attributeName='name')
     * @api
     * @return  string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * returns whether user agent is a bot or not
     *
     * @XmlAttribute(attributeName='isBot')
     * @api
     * @return  bool
     */
    public function isBot()
    {
        if (null === $this->isBot) {
            $this->isBot = false;
            foreach ($this->botUserAgents as $botUserAgent) {
                if (preg_match($botUserAgent, $this->name) === 1) {
                    $this->isBot = true;
                    break;
                }
            }
        }

        return $this->isBot;

    }

    /**
     * returns whether user agent accepts cookies or not
     *
     * @XmlAttribute(attributeName='acceptsCookies')
     * @api
     * @since   2.0.0
     * @return  bool
     */
    public function acceptsCookies()
    {
        return $this->acceptsCookies;
    }

    /**
     * returns a string representation of the class
     *
     * @XmlIgnore
     * @return  string
     */
    public function __toString()
    {
        return $this->name;
    }
}
