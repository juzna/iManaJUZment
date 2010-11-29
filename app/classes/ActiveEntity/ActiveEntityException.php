<?php

namespace ActiveEntity;

class Exception extends \Exception {
    static public function noEntityManager() {
        return new self('No Entity Manager was passed to ActiveEntity::setEntityManager().');
    }
}

