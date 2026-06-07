<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Todo Camisetas API',
    version: '1.0.0',
    description: 'API B2B para gestión de camisetas y clientes'
)]
#[OA\Server(
    url: 'http://localhost:8080',
    description: 'Servidor local Docker'
)]
abstract class Controller
{
}
