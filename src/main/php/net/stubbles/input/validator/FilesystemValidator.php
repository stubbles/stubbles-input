<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\validator;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Class for validating that a string denotes an existing path.
 *
 *
 * @since  2.0.0
 */
abstract class FilesystemValidator extends BaseObject implements Validator
{
    /**
     * allows relative values
     */
    const WITH_RELATIVE    = true;
    /**
     * disallows relative values
     */
    const NO_RELATIVE      = false;
    /**
     * base path where file must reside in
     *
     * @type  string
     */
    private $basePath;
    /**
     * switch whether relative pathes are allowed
     *
     * @type  string
     */
    private $allowRelative = self::NO_RELATIVE;

    /**
     * constructor
     *
     * If no base path is given the validation will be done against the whole
     * file system, given values can not be relative then.
     *
     * @param  string  $basePath
     */
    public function __construct($basePath = null)
    {
        $this->basePath = $basePath;
    }

    /**
     * allow relative values
     *
     * @return  FilesystemValidator
     */
    public function allowRelative()
    {
        $this->allowRelative = self::WITH_RELATIVE;
        return $this;
    }

    /**
     * validate that the given value is represents an existing path
     *
     * @param   string|null  $value
     * @return  bool
     */
    public function validate($value)
    {
        if (empty($value)) {
            return false;
        }

        if (null !== $this->basePath) {
            if (!$this->allowRelative && strstr($value, '..')) {
                return false;
            }

            return $this->fileExists($this->basePath . '/' . $value);
        }

        return $this->fileExists($value);
    }

    /**
     * checks if given file exists and is a file
     *
     * @param   string  $path
     * @return  bool
     */
    protected abstract function fileExists($path);
}
?>