<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker;
/**
 * Helper class for the test.
 */
class BrokerClass
{
    /**
     * test property
     *
     * @type  string
     */
    private $bar     = null;
    /**
     * test property
     *
     * @type  string
     */
    private $baz     = null;
    /**
     * verbosity switch
     *
     * @type  bool
     */
    private $verbose = false;

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
     * test method without parameter
     *
     * @Request[Bool](name='verbose', group='noparam')
     */
    public function enableVerbose()
    {
        $this->verbose = true;
    }

    /**
     * test method
     *
     * @return  bool
     */
    public function isVerbose()
    {
        return $this->verbose;
    }

    /**
     * test method
     *
     * @Request[String](name='bar', group='main')
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
     * @Request[Mock](name='baz', group='other')
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
