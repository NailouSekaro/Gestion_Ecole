<?php

namespace App\Exceptions;

use Exception;

class InvalidTrimestreDatesException extends Exception {
    public function __construct( $message = 'Les dates du trimestre sont invalides.' ) {
        parent::__construct( $message );
    }
}
