<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 28-October-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YunitsForWP\Vendor_Prefixed\DI;

use YunitsForWP\Vendor_Prefixed\DI\Definition\ArrayDefinitionExtension;
use YunitsForWP\Vendor_Prefixed\DI\Definition\EnvironmentVariableDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\Helper\AutowireDefinitionHelper;
use YunitsForWP\Vendor_Prefixed\DI\Definition\Helper\CreateDefinitionHelper;
use YunitsForWP\Vendor_Prefixed\DI\Definition\Helper\FactoryDefinitionHelper;
use YunitsForWP\Vendor_Prefixed\DI\Definition\Reference;
use YunitsForWP\Vendor_Prefixed\DI\Definition\StringDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\ValueDefinition;

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\value')) {
    /**
     * Helper for defining a value.
     *
     * @param mixed $value
     */
    function value($value) : ValueDefinition
    {
        return new ValueDefinition($value);
    }
}

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\create')) {
    /**
     * Helper for defining an object.
     *
     * @param string|null $className Class name of the object.
     *                               If null, the name of the entry (in the container) will be used as class name.
     */
    function create(string $className = null) : CreateDefinitionHelper
    {
        return new CreateDefinitionHelper($className);
    }
}

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\autowire')) {
    /**
     * Helper for autowiring an object.
     *
     * @param string|null $className Class name of the object.
     *                               If null, the name of the entry (in the container) will be used as class name.
     */
    function autowire(string $className = null) : AutowireDefinitionHelper
    {
        return new AutowireDefinitionHelper($className);
    }
}

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\factory')) {
    /**
     * Helper for defining a container entry using a factory function/callable.
     *
     * @param callable $factory The factory is a callable that takes the container as parameter
     *                          and returns the value to register in the container.
     */
    function factory($factory) : FactoryDefinitionHelper
    {
        return new FactoryDefinitionHelper($factory);
    }
}

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\decorate')) {
    /**
     * Decorate the previous definition using a callable.
     *
     * Example:
     *
     *     'foo' => decorate(function ($foo, $container) {
     *         return new CachedFoo($foo, $container->get('cache'));
     *     })
     *
     * @param callable $callable The callable takes the decorated object as first parameter and
     *                           the container as second.
     */
    function decorate($callable) : FactoryDefinitionHelper
    {
        return new FactoryDefinitionHelper($callable, true);
    }
}

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\get')) {
    /**
     * Helper for referencing another container entry in an object definition.
     */
    function get(string $entryName) : Reference
    {
        return new Reference($entryName);
    }
}

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\env')) {
    /**
     * Helper for referencing environment variables.
     *
     * @param string $variableName The name of the environment variable.
     * @param mixed $defaultValue The default value to be used if the environment variable is not defined.
     */
    function env(string $variableName, $defaultValue = null) : EnvironmentVariableDefinition
    {
        // Only mark as optional if the default value was *explicitly* provided.
        $isOptional = 2 === func_num_args();

        return new EnvironmentVariableDefinition($variableName, $isOptional, $defaultValue);
    }
}

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\add')) {
    /**
     * Helper for extending another definition.
     *
     * Example:
     *
     *     'log.backends' => \YunitsForWP\Vendor_Prefixed\DI\add(\YunitsForWP\Vendor_Prefixed\DI\get('My\Custom\LogBackend'))
     *
     * or:
     *
     *     'log.backends' => \YunitsForWP\Vendor_Prefixed\DI\add([
     *         DI\get('My\Custom\LogBackend')
     *     ])
     *
     * @param mixed|array $values A value or an array of values to add to the array.
     *
     * @since 5.0
     */
    function add($values) : ArrayDefinitionExtension
    {
        if (! is_array($values)) {
            $values = [$values];
        }

        return new ArrayDefinitionExtension($values);
    }
}

if (! function_exists('YunitsForWP\Vendor_Prefixed\DI\string')) {
    /**
     * Helper for concatenating strings.
     *
     * Example:
     *
     *     'log.filename' => \YunitsForWP\Vendor_Prefixed\DI\string('{app.path}/app.log')
     *
     * @param string $expression A string expression. Use the `{}` placeholders to reference other container entries.
     *
     * @since 5.0
     */
    function string(string $expression) : StringDefinition
    {
        return new StringDefinition($expression);
    }
}
