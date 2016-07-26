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
namespace stubbles\input\broker\param;
use stubbles\input\Request;
/**
 * Helper interface for tests.
 */
interface WebRequest extends Request
{
    public function readHeader($headerName);

    public function readCookie($cookieName);
}
