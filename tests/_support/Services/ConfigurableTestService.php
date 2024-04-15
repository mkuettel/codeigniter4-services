<?php

namespace Tests\Support\Services;

use MKU\Services\Library\Config\Configurable;
use MKU\Services\Library\Config\ConfigurableTrait;
use PHPUnit\Framework\Assert;
use Tests\Support\Config\ConfigurableTest as ConfigurableTestConfig;

class ConfigurableTestService implements Configurable {
    /** @use ConfigurableTrait<ConfigurableTestConfig> */
    use ConfigurableTrait;

    public function __construct(ConfigurableTestConfig $config) {
        $this->configure($config);
    }

    protected function applyConfig($_ = null, ConfigurableTestConfig $config = null): void {
        Assert::assertNotNull($config);
        Assert::assertIsBool($config->boolean_conf);
        Assert::assertEquals(true, $config->boolean_conf);
        Assert::assertIsString($config->string_conf);
        Assert::assertEquals('some_config_value', $config->string_conf);
    }
}