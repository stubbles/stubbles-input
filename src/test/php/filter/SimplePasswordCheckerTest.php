<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\values\Secret;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\SimplePasswordChecker.
 *
 * @since  3.0.0
 */
#[Group('filter')]
class SimplePasswordCheckerTest extends TestCase
{
    private SimplePasswordChecker $simplePasswordChecker;

    protected function setUp(): void
    {
        $this->simplePasswordChecker = SimplePasswordChecker::create();
    }

    #[Test]
    public function doesNotReportErrorsWithDefaultValuesAndSatisfyingPassword(): void
    {
        assertEmptyArray(
            $this->simplePasswordChecker->check(Secret::create('topsecret'))
        );
    }

    #[Test]
    public function reportsErrorsWithDefaultValuesAndNonSatisfyingPassword(): void
    {
        assertThat(
            $this->simplePasswordChecker->check(Secret::create('ooo')),
            equals([
                'PASSWORD_TOO_SHORT'           => ['minLength' => SimplePasswordChecker::DEFAULT_MINLENGTH],
                'PASSWORD_TOO_LESS_DIFF_CHARS' => ['minDiff'   => SimplePasswordChecker::DEFAULT_MIN_DIFF_CHARS]
            ])
        );
    }

    #[Test]
    public function reportsErrorsWithChangedValuesAndNonSatisfyingPassword(): void
    {
        assertThat(
            $this->simplePasswordChecker->minLength(10)
                ->minDiffChars(8)
                ->check(Secret::create('topsecret')),
            equals([
                'PASSWORD_TOO_SHORT'           => ['minLength' => 10],
                'PASSWORD_TOO_LESS_DIFF_CHARS' => ['minDiff'   => 8]
            ])
        );
    }

    #[Test]
    public function reportsErrorsWithDisallowedValues(): void
    {
        assertThat(
            $this->simplePasswordChecker->disallowValues(['topsecret'])
                ->check(Secret::create('topsecret')),
            equals(['PASSWORD_DISALLOWED' => []])
        );
    }
}
