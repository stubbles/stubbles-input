<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use Generator;

/**
 * @since 9.0.0
 */
class EmptyValuesDataProvider
{
    public static function provideStrings(): Generator
    {
        yield [''];
        yield [null];
    }
}
