<?php

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\BaseException;

class Base extends AbstractRegexRule
{
    public $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public $base;
    public $valid;

    public function __construct($base = null, $chars = null)
    {
        if (!is_null($chars)) {
            $this->chars = $chars;
        }

        $max = strlen($this->chars);
        if (!is_numeric($base) || $base > $max) {
            throw new BaseException(sprintf('a base between 1 and %s is required', $max));
        }
        $this->base = $base;
        $this->valid = substr($this->chars, 0, $this->base);
    }

    public function getPregFormat()
    {
        return  "@^[$this->valid]+$@";
    }
}
