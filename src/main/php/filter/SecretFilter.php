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
namespace stubbles\input\filter;
use stubbles\input\Param;
use stubbles\values\Secret;
/**
 * Class for filtering secrets.
 *
 * @since  3.0.0
 */
class SecretFilter extends StringFilter
{
    use ReusableFilter;

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param         $param
     * @return  \stubbles\values\Secret  filtered string
     */
    public function apply(Param $param): Secret
    {
        $value = parent::apply($param);
        if (!empty($value)) {
            return Secret::create($value);
        }

        return Secret::forNull();
    }
}
