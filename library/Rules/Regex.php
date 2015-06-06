<?php

namespace Respect\Validation\Rules;

class Regex extends AbstractRegexRule
{
    public $regex;

    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    public function getPregFormat()
    {
        return $this->regex;
    }
}
