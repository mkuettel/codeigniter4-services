<?php
declare(strict_types=1);
namespace MKU\Services;

/**
 * The service interface must be implemented by all service classes
 * which wish to take advantage of automated dependency injection (to be implemented).
 */
interface Service {
    /**
     * The shortname is a services identifier
     * and must be unique and can only contain alphanumeric characters and underscores
     *
     * Multiple calls to this method must return the same string.
     *
     * @return string
     */
    public function shortname(): string;
}