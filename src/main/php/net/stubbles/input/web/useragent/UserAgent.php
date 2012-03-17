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
 * Value object for user agents.
 *
 * @since  1.2.0
 * @XmlTag(tagName='userAgent')
 * @ProvidedBy(net\stubbles\input\web\useragent\UserAgentProvider.class)
 */
class UserAgent extends BaseObject
{
    /**
     * name of user agent
     *
     * @type  string
     */
    protected $name;
    /**
     * whether user agent is a bot or not
     *
     * @type  bool
     */
    protected $isBot;

    /**
     * constructor
     *
     * @param  string  $name  name of user agent
     * @param  bool    $isBot whether user agent is a bot or not
     */
    public function __construct($name, $isBot)
    {
        $this->name  = $name;
        $this->isBot = $isBot;
    }

    /**
     * returns name of user agent
     *
     * @XmlAttribute(attributeName='name')
     * @api
     * @return  string
     */
    public function getName()
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
}
?>