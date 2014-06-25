<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\validator;
use stubbles\input\Validator;
/**
 * Class for validating that a string denotes an existing path.
 *
 * @since  2.0.0
 * @deprecated  since 3.0.0, use predicates instead, will be removed with 4.0.0
 */
abstract class FilesystemValidator implements Validator
{
    /**
     * base path where file must reside in
     *
     * @type  string
     */
    private $basePath;

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
