<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\errors\messages;
/**
 * Class containing a localized message.
 *
 * @since  3.0.0
 * @api
 * @XmlTag(tagName='string')
 */
class LocalizedMessage
{
    /**
     * locale of the message
     *
     * @type  string
     */
    private $locale;
    /**
     * content of the message
     *
     * @type  string
     */
    private $message;

    /**
     * constructor
     *
     * @param  string  $locale
     * @param  string  $message
     */
    public function __construct(string $locale, string $message)
    {
        $this->locale  = $locale;
        $this->message = $message;
    }

    /**
     * returns the locale of the message
     *
     * @XmlAttribute(attributeName='locale')
     * @return  string
     */
    public function locale(): string
    {
        return $this->locale;
    }

    /**
     * returns the content of the message
     *
     * @XmlTag(tagName='content')
     * @return  string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * returns a string representation
     *
     * @return  string
     */
    public function __toString(): string
    {
        return $this->message();
    }
}
