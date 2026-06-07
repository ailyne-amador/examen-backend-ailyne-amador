<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ClienteController extends Controller
{
    #[OA\Get(
        path: '/api/clientes',
        summary: 'Listar todos los clientes',
        tags: ['Clientes'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de clientes',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'nombre_comercial', type: 'string', example: '90minutos'),
                            new OA\Property(property: 'rut', type: 'string', example: '76.111.111-1'),
                            new OA\Property(property: 'direccion', type: 'string', example: 'Providencia, Santiago'),
                            new OA\Property(property: 'categoria', type: 'string', example: 'Preferencial'),
                            new OA\Property(property: 'contacto_nombre', type: 'string', example: 'Carlos Ruiz'),
                            new OA\Property(property: 'contacto_email', type: 'string', example: 'carlos@90minutos.cl'),
                            new OA\Property(property: 'porcentaje_oferta', type: 'number', example: 15),
                        ]
                    )
                )
            )
        ]
    )]
    public function index()
    {
        return response()->json(Cliente::all());
    }

    #[OA\Post(
        path: '/api/clientes',
        summary: 'Crear un cliente',
        tags: ['Clientes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre_comercial', 'rut', 'direccion', 'categoria', 'contacto_nombre', 'contacto_email'],
                properties: [
                    new OA\Property(property: 'nombre_comercial', type: 'string', example: 'NuevaTienda'),
                    new OA\Property(property: 'rut', type: 'string', example: '76.333.333-3'),
                    new OA\Property(property: 'direccion', type: 'string', example: 'Vitacura, Santiago'),
                    new OA\Property(property: 'categoria', type: 'string', enum: ['Regular', 'Preferencial'], example: 'Regular'),
                    new OA\Property(property: 'contacto_nombre', type: 'string', example: 'Juan Pérez'),
                    new OA\Property(property: 'contacto_email', type: 'string', example: 'juan@nueva.cl'),
                    new OA\Property(property: 'porcentaje_oferta', type: 'number', example: 5, nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Cliente creado exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 3),
                        new OA\Property(property: 'nombre_comercial', type: 'string', example: 'NuevaTienda'),
                        new OA\Property(property: 'rut', type: 'string', example: '76.333.333-3'),
                        new OA\Property(property: 'categoria', type: 'string', example: 'Regular'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_comercial'  => 'required|string',
            'rut'               => 'required|string|unique:clientes',
            'direccion'         => 'required|string',
            'categoria'         => 'required|in:Regular,Preferencial',
            'contacto_nombre'   => 'required|string',
            'contacto_email'    => 'required|email',
            'porcentaje_oferta' => 'nullable|numeric|min:0|max:100',
        ]);

        return response()->json(Cliente::create($validated), 201);
    }

    #[OA\Get(
        path: '/api/clientes/{id}',
        summary: 'Ver un cliente por ID',
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cliente encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'nombre_comercial', type: 'string', example: '90minutos'),
                        new OA\Property(property: 'rut', type: 'string', example: '76.111.111-1'),
                        new OA\Property(property: 'direccion', type: 'string', example: 'Providencia, Santiago'),
                        new OA\Property(property: 'categoria', type: 'string', example: 'Preferencial'),
                        new OA\Property(property: 'contacto_nombre', type: 'string', example: 'Carlos Ruiz'),
                        new OA\Property(property: 'contacto_email', type: 'string', example: 'carlos@90minutos.cl'),
                        new OA\Property(property: 'porcentaje_oferta', type: 'number', example: 15),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function show($id)
    {
        return response()->json(Cliente::findOrFail($id));
    }

    #[OA\Put(
        path: '/api/clientes/{id}',
        summary: 'Actualizar un cliente',
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nombre_comercial', type: 'string', example: '90minutos Plus'),
                    new OA\Property(property: 'categoria', type: 'string', enum: ['Regular', 'Preferencial'], example: 'Preferencial'),
                    new OA\Property(property: 'porcentaje_oferta', type: 'number', example: 20),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cliente actualizado'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nombre_comercial'  => 'sometimes|string',
            'rut'               => 'sometimes|string|unique:clientes,rut,' . $id,
            'direccion'         => 'sometimes|string',
            'categoria'         => 'sometimes|in:Regular,Preferencial',
            'contacto_nombre'   => 'sometimes|string',
            'contacto_email'    => 'sometimes|email',
            'porcentaje_oferta' => 'nullable|numeric|min:0|max:100',
        ]);

        $cliente->update($validated);
        return response()->json($cliente);
    }

    #[OA\Delete(
        path: '/api/clientes/{id}',
        summary: 'Eliminar un cliente',
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Cliente eliminado'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado']);
    }
}
