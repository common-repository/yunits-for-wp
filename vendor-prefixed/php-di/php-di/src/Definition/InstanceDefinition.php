<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 28-October-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YunitsForWP\Vendor_Prefixed\DI\Definition;

/**
 * Defines injections on an existing class instance.
 *
 * @since  5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class InstanceDefinition implements Definition
{
    /**
     * Instance on which to inject dependencies.
     *
     * @var object
     */
    private $instance;

    /**
     * @var ObjectDefinition
     */
    private $objectDefinition;

    /**
     * @param object $instance
     */
    public function __construct($instance, ObjectDefinition $objectDefinition)
    {
        $this->instance = $instance;
        $this->objectDefinition = $objectDefinition;
    }

    public function getName() : string
    {
        // Name are superfluous for instance definitions
        return '';
    }

    public function setName(string $name)
    {
        // Name are superfluous for instance definitions
    }

    /**
     * @return object
     */
    public function getInstance()
    {
        return $this->instance;
    }

    public function getObjectDefinition() : ObjectDefinition
    {
        return $this->objectDefinition;
    }

    public function replaceNestedDefinitions(callable $replacer)
    {
        $this->objectDefinition->replaceNestedDefinitions($replacer);
    }

    public function __toString()
    {
        return 'Instance';
    }
}
