<?php

if(!function_exists('transaction')) {
    function transaction(\Closure $func, ...$service_params) {
        return service('transaction', ...$service_params)->transact($func);
    }
}
