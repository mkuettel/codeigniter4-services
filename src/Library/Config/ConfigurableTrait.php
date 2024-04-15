<?php

namespace MKU\Services\Library\Config;


use CodeIgniter\Exceptions\ConfigException;

/**
 * A default implementation for configure() and getConfig() methods compatible with the Configurable interface.
 *
 * @author Moritz Küttel
 * @template T of object
 */
trait ConfigurableTrait {

    /**
     * @var ?T $config the current service configuration
     */
    protected $config = null;

    /**
     * @param ?T $config the service configuration object to configure the service with.
     * @return void
     */

    public function configure($config): void {
        $this->config = $config;
        $this->applyConfig(null, $config);
    }

    /**
     * Get the configured configuration object.
     * @return ?T the configuration object last used to configure the service.
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Override the method like this to have a typed config parameter
     *
     * ```php
     * protected function applyConfig($_, YourConfig $config = null): void {
     *  // your implementation
     * }
     * ```
     *
     * You should throw a ConfigException if some configuration is invalid or non-operable.
     *
     * @param ?T $config the configuration object to apply to the service.
     * @return void
     * @throws ConfigException
     */
    abstract protected function applyConfig($__ignore_this_param = null): void;
//    abstract protected function applyConfig(mixed $config): void;

}