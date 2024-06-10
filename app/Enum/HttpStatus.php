<?php

namespace App\Enum;

class HttpStatus
{
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const CONFLICT = 409;
    const INTERNAL_SERVER_ERROR = 500;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;

    public static function getMessage($code)
    {
        switch ($code) {
            case self::BAD_REQUEST:
                return 'Solicitud incorrecta';
            case self::UNAUTHORIZED:
                return 'No autorizado';
            case self::FORBIDDEN:
                return 'Acceso prohibido';
            case self::NOT_FOUND:
                return 'Recurso no encontrado';
            case self::METHOD_NOT_ALLOWED:
                return 'Método no permitido';
            case self::CONFLICT:
                return 'Conflicto';
            case self::INTERNAL_SERVER_ERROR:
                return 'Error interno del servidor';
            case self::SERVICE_UNAVAILABLE:
                return 'Servicio inhabilitado';
            case self::GATEWAY_TIMEOUT:
                return 'Gateway Timeout';
            default:
                return 'Error desconocido';
        }
    }
}

