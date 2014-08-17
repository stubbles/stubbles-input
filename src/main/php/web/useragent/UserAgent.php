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
    private $isBot;
    /**
     * whether user agent accepts cookies or not
     *
     * @type  bool
     */
    private $acceptsCookies;

    /**
     * constructor
     *
     * @param  string  $name            name of user agent
     * @param  bool    $isBot           whether user agent is a bot or not
     * @param  bool    $acceptsCookies  whether user agent accepts cookies or not
     */
    public function __construct($name, $isBot, $acceptsCookies)
    {
        $this->name           = $name;
        $this->isBot          = $isBot;
        $this->acceptsCookies = $acceptsCookies;
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
