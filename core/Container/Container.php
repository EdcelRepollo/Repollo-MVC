<?php // Start Container class file.

declare(strict_types=1); // Strict typing enabled.

// simple dependency container, mo-create ug manage sa objects ug services.
namespace Core\Container; // Namespace for dependency container.

use ReflectionClass; // Used to inspect classes.
use ReflectionNamedType; // Used to inspect constructor parameter types.
use RuntimeException; // Used for container errors.

final class Container // Simple dependency injection container.
{
    /**
     * Registered class bindings; tells what concrete class/callback to use.
     *
     * @var array<class-string, class-string|callable>
     */
    private array $bindings = []; // Map abstract names to concrete builders/classes.

    /**
     * Shared objects; stored here para dili balik-balik ug create.
     *
     * @var array<class-string, object>
     */
    private array $instances = []; // Store shared instances.

    /**
     * Bind interface/abstract to concrete class or callback.
     *
     * @param class-string $abstract
     * @param class-string|callable $concrete
     */
    public function bind(string $abstract, string|callable $concrete): void // Register normal binding.
    {
        $this->bindings[$abstract] = $concrete; // Save binding definition.
    }

    /**
     * Register a binding that should only be created once.
     *
     * @param class-string $abstract
     * @param class-string|callable $concrete
     */
    public function singleton(string $abstract, string|callable $concrete): void // Register shared binding.
    {
        $this->bindings[$abstract] = function (Container $container) use ($abstract, $concrete): object { // Store closure that caches object.
            // Create object only once; next calls reuse the same instance.
            if (! isset($this->instances[$abstract])) { // If no shared instance yet...
                $this->instances[$abstract] = is_callable($concrete) // Decide how to create object.
                    ? $concrete($container) // Use callback builder.
                    : $container->build($concrete); // Build concrete class.
            } // End cache check.

            return $this->instances[$abstract]; // Return cached shared instance.
        }; // End singleton closure.
    }

    /**
     * Store an already-created object in the container.
     *
     * @param class-string $abstract
     */
    public function instance(string $abstract, object $instance): void // Register existing object.
    {
        $this->instances[$abstract] = $instance; // Store object directly.
    }

    /**
     * Resolve class from container; mo create object and its dependencies.
     *
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     */
    public function resolve(string $abstract): object // Resolve object by class/interface.
    {
        // Return existing instance if naa na.
        if (isset($this->instances[$abstract])) { // If already created/shared...
            return $this->instances[$abstract]; // Return stored instance.
        } // End existing instance check.

        // Use registered binding or the class itself.
        $concrete = $this->bindings[$abstract] ?? $abstract; // Find concrete target.

        if (is_callable($concrete)) { // If target is callback...
            return $concrete($this); // Run callback with container.
        } // End callback check.

        return $this->build($concrete); // Build class through reflection.
    }

    /**
     * Build object using reflection; automatic dependency injection ni.
     *
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    private function build(string $class): object // Build class and constructor dependencies.
    {
        // Inspect class so we know its constructor requirements.
        $reflection = new ReflectionClass($class); // Reflect target class.

        if (! $reflection->isInstantiable()) { // Check if class can be created.
            throw new RuntimeException("Class {$class} is not instantiable."); // Stop if abstract/interface.
        } // End instantiable check.

        $constructor = $reflection->getConstructor(); // Get constructor if any.
        if ($constructor === null) { // If no constructor...
            // No constructor means we can create it directly.
            return $reflection->newInstance(); // Create class without args.
        } // End no-constructor check.

        // Resolve each constructor parameter from the container.
        $dependencies = []; // Constructor arguments to pass later.
        foreach ($constructor->getParameters() as $parameter) { // Loop constructor parameters.
            $type = $parameter->getType(); // Read parameter type.
            if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) { // If no class type...
                // Built-in values need defaults because container cannot guess them.
                if ($parameter->isDefaultValueAvailable()) { // If parameter has default value...
                    $dependencies[] = $parameter->getDefaultValue(); // Use default value.
                    continue; // Move to next parameter.
                } // End default check.

                throw new RuntimeException("Cannot resolve parameter {$parameter->getName()} for {$class}."); // Cannot guess scalar.
            } // End scalar/untyped check.

            $dependencies[] = $this->resolve($type->getName()); // Resolve class dependency.
        } // End parameter loop.

        // Create class with resolved dependencies.
        return $reflection->newInstanceArgs($dependencies); // Instantiate with dependencies.
    }
} // End Container class.
