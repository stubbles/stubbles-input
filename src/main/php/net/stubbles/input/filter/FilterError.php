<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\types\LocalizedString;
/**
 * Class containing error messages after filtering values.
 *
 * @XmlTag(tagName='error')
 */
class FilterError extends BaseObject
{
    /**
     * id of the current value error
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
     * @param  string  $id       id of the current value error
     * @param  array   $details  details of what caused the error
     */
    public function __construct($id, array $details = array())
    {
        $this->id     = $id;
        $this->details = $details;
    }

    /**
     * returns the id of the current value error
     *
     * @XmlAttribute(attributeName='id')
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * sets map of locales and messages for this error
     *
     * @param   array  $messages
     * @return  FilterError
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * returns all messages
     *
     * @XmlTag(tagName='messages')
     * @return  LocalizedString[]
     */
    public function getMessages()
    {
        $messages = array();
        foreach ($this->messages as $locale => $message) {
            foreach ($this->details as $key => $detail) {
                $message = str_replace('{' . $key . '}', $this->flattenDetail($detail), $message);
            }

            $messages[] = new LocalizedString($locale, $message);
        }

        return $messages;
    }

    /**
     * flattens the given detail to be used within the message
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