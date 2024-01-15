<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    public function __construct(private string $locale, private string $message) { }

    /**
     * returns the locale of the message
     *
     * @XmlAttribute(attributeName='locale')
     */
    public function locale(): string
    {
        return $this->locale;
    }

    /**
     * returns the content of the message
     *
     * @XmlTag(tagName='content')
     */
    public function message(): string
    {
        return $this->message;
    }

    public function __toString(): string
    {
        return $this->message();
    }
}
