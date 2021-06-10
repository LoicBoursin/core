<?php

declare(strict_types=1);

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Rector\Rules\ApiResourceAnnotationToResourceAttributeRector;
use ApiPlatform\Metadata\Resource;
use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_80);
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $services = $containerConfigurator->services();

    // ApiResource annotation to Resource & operation attributes
    $services->set(ApiResourceAnnotationToResourceAttributeRector::class)
        ->call('configure', [[
            ApiResourceAnnotationToResourceAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline([
                new AnnotationToAttribute(
                    ApiResource::class,
                    Resource::class
                ),
            ]),
            ApiResourceAnnotationToResourceAttributeRector::REMOVE_TAG => false,
        ]])
    ;

    // ApiResource annotation to ApiResource attribute
    $services->set(AnnotationToAttributeRector::class)
        ->call('configure', [[
            AnnotationToAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline([
                new AnnotationToAttribute(
                    ApiResource::class,
                    ApiResource::class
                ),
            ]),
        ]])
    ;
};
