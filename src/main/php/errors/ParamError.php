<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\errors;
use stubbles\input\errors\messages\LocalizedMessage;
/**
 * Class representing parameter errors after filtering parameter values.
 *
 * @XmlTag(tagName='error')
 */
class ParamError implements \JsonSerializable
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
     * constructor
     *
     * @param  string  $id       id of the current param error
     * @param  array   $details  details of what caused the error
     */
    public function __construct($id, array $details = [])
    {
        $this->id      = $id;
        $this->details = $details;
    }

    /**
     * creates an instance from given error id and details
     *
     * In case given error is already an instance of ParamError it is simply
     * returned.
     *
     * @param   \stubbles\input\errors\ParamError|string  $error    id of error or an instance of ParamError
     * @param   array                                     $details  details of what caused the error
     * @return  \stubbles\input\errors\ParamError
     * @throws  \InvalidArgumentException
     */
    public static function fromData($error, array $details = [])
    {
        if ($error instanceof self) {
            return $error;
        }

        if (!is_string($error)) {
            throw new \InvalidArgumentException('Given error must either be an error id or an instance of ' . __CLASS__);
        }

        return new self($error, $details);
    }

    /**
     * returns the id of the current param error
     *
     * @XmlAttribute(attributeName='id')
     * @return  string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * returns details of what caused the error
     *
     * @return  array
     * @since   5.1.0
     * @XmlIgnore
     */
    public function details()
    {
        return $this->details;
    }

    /**
     * fills given list of messages with details
     *
     * @param   array  $templates  map of locales and message templates
     * @return  \stubbles\input\errors\messages\LocalizedMessage[]
     */
    public function fillMessages(array $templates)
    {
        $messages = [];
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
     * @return  \stubbles\input\errors\messages\LocalizedMessage
     */
    public function fillMessage($message, $locale)
    {
        foreach ($this->details as $key => $detail) {
            $message = str_replace('{' . $key . '}', $this->flattenDetail($detail), $message);
        }

        return new LocalizedMessage($locale, $message);
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

    /**
     * returns something that is suitable for json_encode()
     *
     * @return  array
     * @since   4.5.0
     * @XmlIgnore
     */
    public function jsonSerialize()
    {
        return ['id' => $this->id, 'details' => $this->details];
    }
}
