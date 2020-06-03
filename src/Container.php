<?php

namespace Odango\Transmission;

use DI\ContainerBuilder;

class Container {
    static $container;

    static function get(): \DI\Container {
        if (static::$container === null) {
            static::$container = static::create();
        }

        return static::$container;
    }

    static function create() {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(require(__DIR__ . "/../config/container.php"));
        $builder->addDefinitions(require(__DIR__ . "/../config/default.php"));
        return $builder->build();
    }
}
