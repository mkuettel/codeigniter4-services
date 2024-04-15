<?php
declare(strict_types=1);
namespace MKU\Services;

/**
 * The service interface must be implemented by all service classes
 * for which you want to define a shortname (or alias).
 *
 * The shortname must match a method name used to define the service in the Service configuration of
 * your codeigniter application.
 *
 * @author Moritz Küttel
 */
interface Service {
    /**
     * The shortname is a services identifier
     * and must be unique and can only contain alphanumeric characters and underscores and must be a valid php method name.
     *
     * Multiple calls to this method must return the same string.
     *
     * @return string
     */
    public function shortname(): string;
}