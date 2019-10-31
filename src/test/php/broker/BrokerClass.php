<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker;
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
    public static function create(): self
    {
        return new self();
    }

    /**
     * test method without parameter
     *
     * @Request[Bool](paramName='verbose', paramGroup='noparam')
     */
    public function enableVerbose(): void
    {
        $this->verbose = true;
    }

    /**
     * test method
     *
     * @return  bool
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * test method
     *
     * @Request[String](paramName='bar', paramGroup='main')
     * @param  string  $bar
     */
    public function setBar(string $bar): void
    {
        $this->bar = $bar;
    }

    /**
     * test method
     *
     * @return  string
     */
    public function getBar(): ?string
    {
        return $this->bar;
    }

    /**
     * test method
     *
     * @Request[Mock](paramName='baz', paramGroup='other')
     * @param  string  $baz
     */
    public function setBaz(string $baz): void
    {
        $this->baz = $baz;
    }

    /**
     * test method
     *
     * @return  string
     */
    public function getBaz(): ?string
    {
        return $this->baz;
    }
}
