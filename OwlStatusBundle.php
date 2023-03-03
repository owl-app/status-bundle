<?php

declare(strict_types=1);

namespace Owl\Bundle\StatusBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Owl\Bundle\StatusBundle\DependencyInjection\Compiler\RegisterStatusFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OwlStatusBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterStatusFactoryPass());
    }

    /**
     * @psalm-suppress MismatchingDocblockReturnType https://github.com/vimeo/psalm/issues/2345
     */
    protected function getModelNamespace(): string
    {
        return 'Owl\Component\Status\Model';
    }
}
