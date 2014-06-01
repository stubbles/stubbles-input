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
/**
 * Class for validating that a string denotes an existing directory.
 *
 * @api
 * @since  2.0.0
 */
class DirectoryValidator extends FilesystemValidator
{
    /**
     * checks if given path exists and is a directory
     *
     * @param   string  $path
     * @return  bool
     */
    protected function fileExists($path)
    {
        return file_exists($path . '/.') && filetype($path) === 'dir';
    }
}
