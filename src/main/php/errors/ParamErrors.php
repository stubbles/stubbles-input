<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\errors;

use ArrayIterator;
use Traversable;

/**
 * Container for a filter error list.
 *
 * @since  1.3.0
 * @implements  \IteratorAggregate<string,array<string,ParamError>>
 */
class ParamErrors implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * list of errors that occurred while applying a filter on a param
     *
     * @var  array<string,array<string,ParamError>>
     */
    private array $errors = [];

    /**
     * appends an error to the list of errors for given param name
     *
     * @param   array<string,mixed>  $details    details of what caused the error
     * @since   2.3.3
     */
    public function append(
        string $paramName,
        ParamError|string $error,
        array $details = []
    ): ParamError {
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
     */
    public function count(): int
    {
        return count($this->errors);
    }

    /**
     * checks whether there are any errors at all
     *
     * @api
     */
    public function exist(): bool
    {
        return $this->count() > 0;
    }

    /**
     * checks whether a param has any error
     *
     * @api
     */
    public function existFor(string $paramName): bool
    {
        return isset($this->errors[$paramName]);
    }

    /**
     * checks whether a param has a specific error
     *
     * @api
     */
    public function existForWithId(string $paramName, string $errorId): bool
    {
        return isset($this->errors[$paramName]) && isset($this->errors[$paramName][$errorId]);
    }

    /**
     * returns a list of errors for given param
     *
     * @return  ParamError[]
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
     */
    public function getForWithId(string $paramName, string $errorId): ?ParamError
    {
        if (isset($this->errors[$paramName]) && isset($this->errors[$paramName][$errorId])) {
            return $this->errors[$paramName][$errorId];
        }

        return null;
    }

    /**
     * provides an iterator to iterate over all errors
     *
     * @return  Traversable<string,array<string,ParamError>>
     * @since   2.0.0
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->errors);
    }

    /**
     * returns something that is suitable for json_encode()
     *
     * @return  array<string,mixed>
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
