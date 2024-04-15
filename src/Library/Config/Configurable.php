<?php
declare(strict_types=1);
namespace MKU\Services\Library\Config;

/**
 * @author Moritz Küttel
 * @template T of object
 */
interface Configurable {
    /**
     * Configure or reconfigure the object with the given configuration
     *
     * @param ?T $config
     * @return void
     */
    public function configure($config): void;

    /**
     * Get the current service configuration object.
     *
     * @return ?T the current configuration object.
     */
    public function getConfig();
}