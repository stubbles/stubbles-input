<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace org\stubbles\input\test;
use net\stubbles\lang\BaseObject;
/**
 * Helper class for the test.
 */
class BrokerClass extends BaseObject
{
    /**
     * test property
     *
     * @type  string
     */
    private $bar = null;
    /**
     * test property
     *
     * @type  string
     */
    private $baz = null;

    /**
     * constructor
     */
    public function __construct()
    {
        // intentionally empty
    }

    /**
     * some static method
     *
     * @return  BrokerClass
     */
    public static function create()
    {
        return new self();
    }

    /**
     * test method
     *
     * @Filter[StringFilter](fieldName='bar', group='main')
     * @param  string  $bar
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    /**
     * test method
     *
     * @return  string
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * test method
     *
     * @Filter[MockFilter](fieldName='baz', group='other')
     * @param  string  $baz
     */
    public function setBaz($baz)
    {
        $this->baz = $baz;
    }

    /**
     * test method
     *
     * @return  string
     */
    public function getBaz()
    {
        return $this->baz;
    }
}
?>