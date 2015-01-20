<?php
/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */
namespace Respect\Validation;

use Respect\Validation\Exceptions\AbstractCompositeException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Rules\AllOf;

/**
 * Main validator class.
 */
class Validator extends AllOf
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Factory
     */
    protected static $defaultFactory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory = null)
    {
        $this->factory = $factory ?: static::getDefaultFactory();
    }

    public static function getDefaultFactory()
    {
        if (null === static::$defaultFactory) {
            static::$defaultFactory = new Factory();
        }

        return static::$defaultFactory;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $ruleName
     * @param array  $arguments
     *
     * @return Validator
     */
    public static function __callStatic($ruleName, array $arguments)
    {
        $validator = new static();
        $validator->__call($ruleName, $arguments);

        return $validator;
    }

    /**
     * @param string $ruleName
     * @param array  $arguments
     *
     * @return self
     */
    public function __call($ruleName, array $arguments)
    {
        $rule = $this->factory->rule($ruleName, $arguments);

        $this->addRule($rule);

        return $this;
    }

    /**
     * @return bool
     */
    private function isEmpty($input)
    {
        return (null === $input);
    }

    /**
     * @param mixed  $input
     * @param Result $result
     *
     * @return bool
     */
    public function validate($input)
    {
        if ($this->isEmpty($input)) {
            return true;
        }

        $result = $this->factory->result($this, $input);
        $result->setParam('name', $this->getName());
        $result->applyRule();

        return $result->isValid();
    }

    /**
     * @param mixed  $input
     * @param Result $result
     *
     * @throws ValidationException
     *
     * @return null
     */
    public function check($input)
    {
        if ($this->isEmpty($input)) {
            return;
        }

        $result = $this->factory->result($this, $input);
        foreach ($this->getRules() as $childRule) {
            $childResult = $result->createChild($childRule, $input);
            $childResult->appendTo($result);
            $childResult->setParam('name', $this->getName());
            $childResult->applyRule();

            if ($childResult->isValid()) {
                continue;
            }

            $result->setValid(false);

            throw $this->factory->exception($childResult);
        }
    }

    /**
     * @param mixed  $input
     * @param Result $result
     *
     * @throws AbstractCompositeException
     *
     * @return null
     */
    public function assert($input)
    {
        if ($this->isEmpty($input)) {
            return;
        }

        $result = $this->factory->result($this, $input);
        $result->setParam('name', $this->getName());
        $result->applyRule();
        if ($result->isValid()) {
            return;
        }

        throw $this->factory->exception($result);
    }
}
