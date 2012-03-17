<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\web\useragent;
use net\stubbles\input\Param;
use net\stubbles\input\filter\Filter;
use net\stubbles\lang\BaseObject;
/**
 * Filter to detect a user agent.
 *
 * @since  1.2.0
 */
class UserAgentFilter extends BaseObject implements Filter
{
    /**
     * user agent detector to be used
     *
     * @type  UserAgentDetector
     */
    protected $userAgentDetector;

    /**
     * constructor
     *
     * @param  UserAgentDetector  $userAgentDetector
     * @Inject
     */
    public function  __construct(UserAgentDetector $userAgentDetector)
    {
        $this->userAgentDetector = $userAgentDetector;
    }

    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  mixed  filtered value
     */
    public function apply(Param $param)
    {
        return $this->userAgentDetector->detect($param->getValue());
    }
}
?>