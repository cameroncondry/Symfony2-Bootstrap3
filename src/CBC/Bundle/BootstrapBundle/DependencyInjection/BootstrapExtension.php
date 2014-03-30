<?php

namespace CBC\Bundle\BootstrapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BootstrapExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (!isset($bundles['TwigBundle']) || !isset($bundles['AsseticBundle'])) {
            throw new \Exception('Missing required TwigBundle and AsseticBundle for PLTWBootstrapBundle.');
        }

        // add the form builder to the twig form resources
        $config = ['form' => ['resources' => ['BootstrapBundle:Form:form_div_layout.html.twig']]];
        $container->prependExtensionConfig('twig', $config);

        // add bootstrap to assetic
        $config = ['bundles' => ['BootstrapBundle']];
        $container->prependExtensionConfig('assetic', $config);

        // add the bootstrap fonts to assetic
        $asset_dir = '%kernel.root_dir%/../vendor/twitter/bootstrap/dist/fonts/';
        $assets = [
            'bootstrap_woff_font' => [
                'inputs' => [$asset_dir . 'glyphicons-halflings-regular.woff'],
                'output' => 'fonts/glyphicons-halflings-regular.woff'
            ],
            'bootstrap_ttf_font' => [
                'inputs' => [$asset_dir . 'glyphicons-halflings-regular.ttf'],
                'output' => 'fonts/glyphicons-halflings-regular.ttf'
            ],
            'bootstrap_svg_font' => [
                'inputs' => [$asset_dir . 'glyphicons-halflings-regular.svg'],
                'output' => 'fonts/glyphicons-halflings-regular.svg'
            ]
        ];

        $container->prependExtensionConfig('assetic', ['assets' => $assets]);
    }
}
