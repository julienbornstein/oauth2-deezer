<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return static function (RectorConfig $rectorConfig): void {
    // register single rule
    $rectorConfig->rule(TypedPropertyFromStrictConstructorRector::class);

    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets with your IDE
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_73,
        PHPUnitSetList::PHPUNIT_90,
        SetList::CODE_QUALITY,
    ]);
};
