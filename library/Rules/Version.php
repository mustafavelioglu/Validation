<?php

namespace Respect\Validation\Rules;

/**
 * @link http://semver.org/
 */
class Version extends AbstractRegexRule
{
    public function getPregFormat()
    {
        return '/^[0-9]+\.[0-9]+\.[0-9]+([+-][^+-][0-9A-Za-z-.]*)?$/';
    }
}
