<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 28-October-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YunitsForWP\Vendor_Prefixed\DI\Definition\Resolver;

use YunitsForWP\Vendor_Prefixed\DI\Definition\ArrayDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\DecoratorDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\Definition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\EnvironmentVariableDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\Exception\InvalidDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\FactoryDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\InstanceDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\ObjectDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Definition\SelfResolvingDefinition;
use YunitsForWP\Vendor_Prefixed\DI\Proxy\ProxyFactory;
use YunitsForWP\Vendor_Prefixed\Psr\Container\ContainerInterface;

/**
 * Dispatches to more specific resolvers.
 *
 * Dynamic dispatch pattern.
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ResolverDispatcher implements DefinitionResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ProxyFactory
     */
    private $proxyFactory;

    private $arrayResolver;
    private $factoryResolver;
    private $decoratorResolver;
    private $objectResolver;
    private $instanceResolver;
    private $envVariableResolver;

    public function __construct(ContainerInterface $container, ProxyFactory $proxyFactory)
    {
        $this->container = $container;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * Resolve a definition to a value.
     *
     * @param Definition $definition Object that defines how the value should be obtained.
     * @param array      $parameters Optional parameters to use to build the entry.
     *
     * @throws InvalidDefinition If the definition cannot be resolved.
     *
     * @return mixed Value obtained from the definition.
     */
    public function resolve(Definition $definition, array $parameters = [])
    {
        // Special case, tested early for speed
        if ($definition instanceof SelfResolvingDefinition) {
            return $definition->resolve($this->container);
        }

        $definitionResolver = $this->getDefinitionResolver($definition);

        return $definitionResolver->resolve($definition, $parameters);
    }

    public function isResolvable(Definition $definition, array $parameters = []) : bool
    {
        // Special case, tested early for speed
        if ($definition instanceof SelfResolvingDefinition) {
            return $definition->isResolvable($this->container);
        }

        $definitionResolver = $this->getDefinitionResolver($definition);

        return $definitionResolver->isResolvable($definition, $parameters);
    }

    /**
     * Returns a resolver capable of handling the given definition.
     *
     * @throws \RuntimeException No definition resolver was found for this type of definition.
     */
    private function getDefinitionResolver(Definition $definition) : DefinitionResolver
    {
        switch (true) {
            case $definition instanceof ObjectDefinition:
                if (! $this->objectResolver) {
                    $this->objectResolver = new ObjectCreator($this, $this->proxyFactory);
                }

                return $this->objectResolver;
            case $definition instanceof DecoratorDefinition:
                if (! $this->decoratorResolver) {
                    $this->decoratorResolver = new DecoratorResolver($this->container, $this);
                }

                return $this->decoratorResolver;
            case $definition instanceof FactoryDefinition:
                if (! $this->factoryResolver) {
                    $this->factoryResolver = new FactoryResolver($this->container, $this);
                }

                return $this->factoryResolver;
            case $definition instanceof ArrayDefinition:
                if (! $this->arrayResolver) {
                    $this->arrayResolver = new ArrayResolver($this);
                }

                return $this->arrayResolver;
            case $definition instanceof EnvironmentVariableDefinition:
                if (! $this->envVariableResolver) {
                    $this->envVariableResolver = new EnvironmentVariableResolver($this);
                }

                return $this->envVariableResolver;
            case $definition instanceof InstanceDefinition:
                if (! $this->instanceResolver) {
                    $this->instanceResolver = new InstanceInjector($this, $this->proxyFactory);
                }

                return $this->instanceResolver;
            default:
                throw new \RuntimeException('No definition resolver was configured for definition of type ' . get_class($definition));
        }
    }
}
