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
namespace stubbles\input\errors;
/**
 * Container for a filter error list.
 *
 * @since  1.3.0
 */
class ParamErrors implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * list of errors that occurred while applying a filter on a param
     *
     * @type  array
     */
    private $errors = [];

    /**
     * appends an error to the list of errors for given param name
     *
     * @param   string                                    $paramName  name of parameter to add error for
     * @param   \stubbles\input\errors\ParamError|string  $error      id of error or an instance of ParamError
     * @param   array                                     $details    details of what caused the error
     * @return  \stubbles\input\errors\ParamError
     * @since   2.3.3
     */
    public function append(string $paramName, $error, array $details = []): ParamError
    {
        $error = ParamError::fromData($error, $details);
        if (!isset($this->errors[$paramName])) {
            $this->errors[$paramName] = [$error->id() => $error];
        } else {
            $this->errors[$paramName][$error->id()] = $error;
        }

        return $error;
    }

    /**
     * returns number of collected errors
     *
     * @return  int
     */
    public function count(): int
    {
        return count($this->errors);
    }

    /**
     * checks whether there are any errors at all
     *
     * @api
     * @return  bool
     */
    public function exist(): bool
    {
        return ($this->count() > 0);
    }

    /**
     * checks whether a param has any error
     *
     * @api
     * @param   string  $paramName  name of parameter
     * @return  bool
     */
    public function existFor(string $paramName): bool
    {
        return isset($this->errors[$paramName]);
    }

    /**
     * checks whether a param has a specific error
     *
     * @api
     * @param   string  $paramName  name of parameter
     * @param   string  $errorId    id of error
     * @return  bool
     */
    public function existForWithId(string $paramName, string $errorId): bool
    {
        return (isset($this->errors[$paramName]) && isset($this->errors[$paramName][$errorId]));
    }

    /**
     * returns a list of errors for given param
     *
     * @param   string  $paramName
     * @return  \stubbles\input\errors\ParamError[]
     */
    public function getFor(string $paramName): array
    {
        if (isset($this->errors[$paramName])) {
            return $this->errors[$paramName];
        }

        return [];
    }

    /**
     * returns the error for given param and error id
     *
     * @param   string  $paramName  name of param
     * @param   string  $errorId    id of error
     * @return  \stubbles\input\errors\ParamError|null
     */
    public function getForWithId(string $paramName, string $errorId)
    {
        if (isset($this->errors[$paramName]) && isset($this->errors[$paramName][$errorId])) {
            return $this->errors[$paramName][$errorId];
        }

        return null;
    }

    /**
     * provides an iterator to iterate over all errors
     *
     * @link    http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return  \Traversable
     * @since   2.0.0
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->errors);
    }

    /**
     * returns something that is suitable for json_encode()
     *
     * @return  array
     * @since   4.5.0
     * @XmlIgnore
     */
    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->errors as $paramName => $errors) {
            $result[$paramName] = [
                    'field'  => $paramName,
                    'errors' => array_values($errors)
            ];
        }

        return ['errors' => $result];
    }

}
