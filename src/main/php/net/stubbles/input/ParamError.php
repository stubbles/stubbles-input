<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\types\LocalizedString;
/**
 * Class representing parameter errors after filtering parameter values.
 *
 * @XmlTag(tagName='error')
 */
class ParamError extends BaseObject
{
    /**
     * id of the current param error
     *
     * @type  string
     */
    private $id;
    /**
     * details of what caused the error
     *
     * @type  array
     */
    private $details;
    /**
     * map of locales and messages
     *
     * @type  array
     */
    private $messages = array();

    /**
     * constructor
     *
     * @param  string  $id       id of the current param error
     * @param  array   $details  details of what caused the error
     */
    public function __construct($id, array $details = array())
    {
        $this->id      = $id;
        $this->details = $details;
    }

    /**
     * returns the id of the current param error
     *
     * @XmlAttribute(attributeName='id')
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * fills given list of messages with details
     *
     * @param   array  $templates  map of locales and message templates
     * @return  LocalizedString[]
     */
    public function fillMessages(array $templates)
    {
        $messages = array();
        foreach ($templates as $locale => $message) {
            $messages[] = $this->fillMessage($message, $locale);
        }

        return $messages;
    }

    /**
     * fills given message with details
     *
     * @param   string  $message  message template to fill up
     * @param   string  $locale   locale of the message
     * @return  LocalizedString
     */
    public function fillMessage($message, $locale)
    {
        foreach ($this->details as $key => $detail) {
            $message = str_replace('{' . $key . '}', $this->flattenDetail($detail), $message);
        }

        return new LocalizedString($locale, $message);
    }

    /**
     * flattens the given detail to be used within a message
     *
     * @param   mixed   $detail
     * @return  string
     */
    private function flattenDetail($detail)
    {
        if (is_array($detail)) {
            return join(', ', $detail);
        } elseif (is_object($detail) && !method_exists($detail, '__toString')) {
            return get_class($detail);
        }

        return (string) $detail;
    }
}
?>