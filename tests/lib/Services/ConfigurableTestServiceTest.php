<?php

namespace Tests\Lib\Services;

use PHPUnit\Framework\TestCase;
use \Config\ConfigurableTest as ConfigurableTestConfig;
use Tests\Support\Services\ConfigurableTestService;

class ConfigurableTestServiceTest extends TestCase {

    public function testManualInstantiation(): void {
        $config = config('ConfigurableTest');
        $this->assertInstanceOf(ConfigurableTestConfig::class, $config);

        $service = new ConfigurableTestService($config);
        $this->assertNotNull($service);
        $this->assertNotNull($service->getConfig());
        $this->assertEquals($config, $service->getConfig());
    }

    public function testServiceInstantiation(): void {
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
