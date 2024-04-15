<?php

namespace Tests\Support\Services;

use PHPUnit\Framework\TestCase;
use Tests\Support\Config\ConfigurableTest as ConfigurableTestConfig;

class ConfigurableTestServiceTest extends TestCase {

    public function testManualInstatiation(): void {
        $config = config('ConfigurableTest');
        $service = new ConfigurableTestService($config);
        $this->assertEquals($config, $service->getConfig());
    }

    public function testServiceInstatiation(): void {
        $config = config('ConfigurableTest');
        $service = service('test_configurable');
        $this->assertInstanceOf(ConfigurableTestService::class, $service);
        $this->assertEquals($config, $service->getConfig());
    }

    public function testConfigure(): void {
        $config = new ConfigurableTestConfig();
        $service = service('test_configurable', $config);
        $service->configure($config);
    }
}
