<?php

declare(strict_types=1);

namespace ProxyManager\Factory;

use ProxyManager\Proxy\VirtualProxyInterface;
use ProxyManager\ProxyGenerator\LazyLoadingValueHolderGenerator;
use ProxyManager\ProxyGenerator\ProxyGeneratorInterface;

/**
 * Factory responsible of producing virtual proxy instances
 *
 */
class LazyLoadingValueHolderFactory extends AbstractBaseFactory
{
    /** @var LazyLoadingValueHolderGenerator|null */
    private $generator;

    public function createProxy(
        string $className,
        \Closure $initializer,
        array $proxyOptions = []
    ) : VirtualProxyInterface {
        $proxyClassName = $this->generateProxy($className, $proxyOptions);

        return $proxyClassName::staticProxyConstructor($initializer);
    }

    /**
     * {@inheritDoc}
     */
    protected function getGenerator() : ProxyGeneratorInterface
    {
        return $this->generator ?: $this->generator = new LazyLoadingValueHolderGenerator();
    }
}
