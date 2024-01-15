<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use Override;
use stubbles\input\filter\SimplePasswordChecker;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
use stubbles\values\Secret;

/**
 * Filter passwords based on a @Request[Password] annotation.
 */
class PasswordParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?Secret
    {
        return $valueReader->asPassword(SimplePasswordChecker::create()
                ->minDiffChars($annotation->getMinDiffChars(SimplePasswordChecker::DEFAULT_MIN_DIFF_CHARS))
                ->minLength($annotation->getMinLength(SimplePasswordChecker::DEFAULT_MINLENGTH))
        );
    }

    #[Override]
    protected function supportsDefault(): bool
    {
        return false;
    }
}
