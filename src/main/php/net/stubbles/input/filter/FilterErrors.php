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
/**
 * Container for a filter error list.
 *
 * @since  1.3.0
 */
class FilterErrors extends BaseObject implements \IteratorAggregate
{
    /**
     * list of errors that occurred while applying a filter on a request value
     *
     * @type  array
     */
    private $errors = array();

    /**
     * adds error with given id for given parameter name
     *
     * @param   FilterError  $error      error to add
     * @param   string       $paramName  name of parameter to add error for
     * @return  FilterError
     */
    public function add(FilterError $error, $paramName)
    {
        if (isset($this->errors[$paramName]) === false) {
            $this->errors[$paramName] = array($error->getId() => $error);
        } else {
            $this->errors[$paramName][$error->getId()] = $error;
        }

        return $error;
    }

    /**
     * returns number of collected errors
     *
     * @return  int
     */
    public function count()
    {
        return count($this->errors);
    }

    /**
     * checks whether there are any errors at all
     *
     * @return  bool
     */
    public function exist()
    {
        return ($this->count() > 0);
    }

    /**
     * checks whether a request value has any error
     *
     * @param   string  $paramName  name of parameter
     * @return  bool
     */
    public function existFor($paramName)
    {
        return isset($this->errors[$paramName]);
    }

    /**
     * checks whether a request value has a specific error
     *
     * @param   string  $paramName  name of parameter
     * @param   string  $errorId    id of error
     * @return  bool
     */
    public function existForWithId($paramName, $errorId)
    {
        return (isset($this->errors[$paramName]) && isset($this->errors[$paramName][$errorId]));
    }

    /**
     * returns list of all errors for all request values
     *
     * @return  array
     */
    public function get()
    {
        return $this->errors;
    }

    /**
     * returns a list of errors for given request value
     *
     * @param   string  $paramName
     * @return  FilterError[]
     */
    public function getFor($paramName)
    {
        if (isset($this->errors[$paramName]) === true) {
            return $this->errors[$paramName];
        }

        return array();
    }

    /**
     * returns the error for given request value and error id
     *
     * @param   string  $paramName  name of request value
     * @param   string  $errorId    id of error
     * @return  FilterError
     */
    public function getForWithId($paramName, $errorId)
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
     * @return  Traversable
     * @since   2.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->errors);
    }
}
?>