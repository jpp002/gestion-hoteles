<?php

namespace App\Exceptions;

use Exception;

class HuespedNotFoundException extends Exception
{
    protected $huespedId;

    public function __construct($huespedId = null)
    {
        $this->huespedId = $huespedId;
        $message = $huespedId 
            ? "El huesped con ID {$huespedId} no existee." 
            : "El huesped no existe.";
        parent::__construct($message);
    }

    public function render($request)
    {
        return response()->json([
            'Error' => $this->getMessage(),
        ], 404);
    }
}
