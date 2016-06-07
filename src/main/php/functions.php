<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\predicate {

    /**
     * negates evaluation of given predicate
     *
     * @api
     * @param   \stubbles\input\predicate\Predicate|callable  $predicate
     * @return  \stubbles\input\predicate\Predicate
     * @since   6.0.0
     */
    function not($predicate)
    {
        $predicate = Predicate::castFrom($predicate);
        return new CallablePredicate(
                function($value) use ($predicate)
                {
                    return !$predicate->test($value);
                }
        );
    }
}
