<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use stubbles\input\errors\ParamError;
/**
 * Container for a parameter and its value.
 *
 * @since  2.0.0
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class Param
{
    /**
     * name of param
     *
     * @var  string
     */
    private $name;
    /**
     * original value
     *
     * @var  string|string[]
     */
    private $value;
    /**
     * list of error ids for this param
     *
     * @var  ParamError[]
     */
    private $errors = [];

    /**
     * constructor
     *
     * @param  string        $name   name of param
     * @param  string|string[]|null  $value  original value
     */
    public function __construct(string $name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * returns name of param
     *
     * @return  string
     * @since   3.0.0
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * returns value of param
     *
     * @return  string|string[]|null
     * @since   3.0.0
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * checks if parameter is null
     *
     * @return  bool
     */
    public function isNull(): bool
    {
        return null === $this->value;
    }

    /**
     * checks if parameter is empty
     *
     * Parameter is empty if its value is null or an empty string.
     *
     * @return  bool
     */
    public function isEmpty(): bool
    {
        return $this->isNull()
            || (is_array($this->value) && count($this->value) === 0)
            || $this->length() === 0;
    }

    /**
     * returns length of value
     *
     * @return  int
     */
    public function length(): int
    {
        if ($this->isNull() || (is_string($this->value) && strlen($this->value) === 0)) {
            return 0;
        }

        if (is_string($this->value)) {
            return strlen($this->value);
        }

        return strlen(current($this->value));
    }

    /**
     * adds error with given id
     *
     * @param   \stubbles\input\errors\ParamError|string  $error    id of error or an instance of ParamError
     * @param   array<string,mixed>                       $details  details of what caused the error
     * @return  \stubbles\input\errors\ParamError
     */
    public function addError($error, array $details = []): ParamError
    {
        $error = ParamError::fromData($error, $details);
        $this->errors[$error->id()] = $error;
        return $error;
    }

    /**
     * checks if param has error with given id
     *
     * @param   string  $errorId
     * @return  bool
     */
    public function hasError($errorId): bool
    {
        return isset($this->errors[$errorId]);
    }

    /**
     * checks if param has any errors
     *
     * @return  bool
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * returns list of error ids
     *
     * @return  \stubbles\input\errors\ParamError[]
     * @since   3.0.0
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
