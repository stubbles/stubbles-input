<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use PHPUnit\Framework\TestCase;
use stubbles\input\errors\ParamError;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\Param.
 *
 * @group  core
 */
class ParamTest extends TestCase
{
    /**
     * @test
     */
    public function returnsGivenName(): void
    {
        $param = new Param('foo', 'bar');
        assertThat($param->name(), equals('foo'));
    }

    /**
     * @test
     */
    public function returnsGivenValue(): void
    {
        $param = new Param('foo', 'bar');
        assertThat($param->value(), equals('bar'));
    }

    /**
     * @test
     */
    public function isNullIfValueIsNull(): void
    {
        $param = new Param('foo', null);
        assertTrue($param->isNull());
    }

    /**
     * @test
     */
    public function isEmptyIfValueIsNull(): void
    {
        $param = new Param('foo', null);
        assertTrue($param->isEmpty());
    }

    /**
     * @test
     */
    public function isEmptyIfValueIsEmptyString(): void
    {
        $param = new Param('foo', '');
        assertTrue($param->isEmpty());
    }

    /**
     * @test
     */
    public function returnsValueLength(): void
    {
        $param = new Param('foo', 'bar');
        assertThat($param->length(), equals(3));
    }

    /**
     * @test
     */
    public function hasNoErrorByDefault(): void
    {
        $param = new Param('foo', 'bar');
        assertFalse($param->hasErrors());
    }

    /**
     * @test
     */
    public function hasEmptyErrorListByDefault(): void
    {
        $param = new Param('foo', 'bar');
        assertEmptyArray($param->errors());
    }

    /**
     * @test
     */
    public function hasErrorIfAddedWithId(): void
    {
        $param = new Param('foo', 'bar');
        $param->addError('SOME_ERROR');
        assertTrue($param->hasErrors());
    }

    /**
     * @test
     */
    public function hasNonEmptyErrorListIfErrorAddedWithIdAndDetails(): void
    {
        $param = new Param('foo', 'bar');
        $error = $param->addError('SOME_ERROR', ['some' => 'detail']);
        assertThat($param->errors(), equals(['SOME_ERROR' => $error]));
    }

    /**
     * @test
     * @since  2.3.3
     * @group  issue_46
     */
    public function hasErrorIfAddedAsInstance(): void
    {
        $param = new Param('foo', 'bar');
        $param->addError('SOME_ERROR');
        assertTrue($param->hasErrors());
    }

    /**
     * @test
     * @since  2.3.3
     * @group  issue_46
     */
    public function hasNonEmptyErrorListIfErrorAddedAsInstance(): void
    {
        $param = new Param('foo', 'bar');
        $error = $param->addError('SOME_ERROR', ['some' => 'detail']);
        assertThat($param->errors(), equals(['SOME_ERROR' => $error]));
    }

    /**
     * @test
     * @since  2.3.3
     * @group  issue_46
     */
    public function addNonParamErrorAndNoErrorIdResultsInInvalidArgumentException(): void
    {
        $param = new Param('foo', 'bar');
        expect(function() use ($param) {
                $param->addError(500);
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage(
                'Given error must either be an error id or an instance of '
                . ParamError::class
        );
    }
}
