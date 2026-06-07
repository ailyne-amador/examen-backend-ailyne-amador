<?php

namespace App\Http\Controllers;

use App\Models\Camiseta;
use App\Models\Cliente;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CamisetaController extends Controller
{
    #[OA\Get(
        path: '/api/camisetas',
        summary: 'Listar todas las camisetas',
        tags: ['Camisetas'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de camisetas con sus tallas')
        ]
    )]
    public function index()
    {
        return response()->json(Camiseta::with('tallas')->get());
    }

    #[OA\Post(
        path: '/api/camisetas',
        summary: 'Crear una camiseta',
        tags: ['Camisetas'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'club', 'pais', 'tipo', 'color', 'precio', 'codigo_producto'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string', example: 'Camiseta Local 2025'),
                    new OA\Property(property: 'club', type: 'string', example: 'Selección Chilena'),
                    new OA\Property(property: 'pais', type: 'string', example: 'Chile'),
                    new OA\Property(property: 'tipo', type: 'string', example: 'Local'),
                    new OA\Property(property: 'color', type: 'string', example: 'Rojo y Azul'),
                    new OA\Property(property: 'precio', type: 'number', example: 45000),
                    new OA\Property(property: 'precio_oferta', type: 'number', example: 38000, nullable: true),
                    new OA\Property(property: 'detalles', type: 'string', nullable: true),
                    new OA\Property(property: 'codigo_producto', type: 'string', example: 'SCL2025L'),
                    new OA\Property(property: 'tallas', type: 'array', items: new OA\Items(type: 'integer')),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Camiseta creada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'          => 'required|string',
            'club'            => 'required|string',
            'pais'            => 'required|string',
            'tipo'            => 'required|string',
            'color'           => 'required|string',
            'precio'          => 'required|numeric|min:0',
            'precio_oferta'   => 'nullable|numeric|min:0',
            'detalles'        => 'nullable|string',
            'codigo_producto' => 'required|string|unique:camisetas',
            'tallas'          => 'nullable|array',
            'tallas.*'        => 'exists:tallas,id',
        ]);

        $camiseta = Camiseta::create($validated);

        if (!empty($validated['tallas'])) {
            $camiseta->tallas()->sync($validated['tallas']);
        }

        return response()->json($camiseta->load('tallas'), 201);
    }

    #[OA\Get(
        path: '/api/camisetas/{id}',
        summary: 'Ver una camiseta por ID',
        tags: ['Camisetas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Camiseta encontrada'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function show($id)
    {
        return response()->json(Camiseta::with('tallas')->findOrFail($id));
    }

    #[OA\Put(
        path: '/api/camisetas/{id}',
        summary: 'Actualizar una camiseta',
        tags: ['Camisetas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'precio', type: 'number'),
                    new OA\Property(property: 'precio_oferta', type: 'number', nullable: true),
                    new OA\Property(property: 'tallas', type: 'array', items: new OA\Items(type: 'integer')),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Camiseta actualizada'),
            new OA\Response(response: 404, description: 'No encontrada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $camiseta = Camiseta::findOrFail($id);

        $validated = $request->validate([
            'titulo'          => 'sometimes|string',
            'club'            => 'sometimes|string',
            'pais'            => 'sometimes|string',
            'tipo'            => 'sometimes|string',
            'color'           => 'sometimes|string',
            'precio'          => 'sometimes|numeric|min:0',
            'precio_oferta'   => 'nullable|numeric|min:0',
            'detalles'        => 'nullable|string',
            'codigo_producto' => 'sometimes|string|unique:camisetas,codigo_producto,' . $id,
            'tallas'          => 'nullable|array',
            'tallas.*'        => 'exists:tallas,id',
        ]);

        $camiseta->update($validated);

        if (isset($validated['tallas'])) {
            $camiseta->tallas()->sync($validated['tallas']);
        }

        return response()->json($camiseta->load('tallas'));
    }

    #[OA\Delete(
        path: '/api/camisetas/{id}',
        summary: 'Eliminar una camiseta',
        tags: ['Camisetas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Camiseta eliminada'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function destroy($id)
    {
        $camiseta = Camiseta::findOrFail($id);
        $camiseta->tallas()->detach();
        $camiseta->delete();
        return response()->json(['message' => 'Camiseta eliminada']);
    }

    #[OA\Get(
        path: '/api/camisetas/{id}/precio',
        summary: 'Precio final según cliente',
        tags: ['Camisetas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'cliente_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Precio final calculado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function precioFinal($id, Request $request)
    {
        $camiseta = Camiseta::findOrFail($id);
        $cliente  = Cliente::findOrFail($request->query('cliente_id'));

        return response()->json([
            'camiseta_id'   => $camiseta->id,
            'titulo'        => $camiseta->titulo,
            'cliente'       => $cliente->nombre_comercial,
            'precio_base'   => $camiseta->precio,
            'precio_oferta' => $camiseta->precio_oferta,
            'precio_final'  => $camiseta->getPrecioFinal($cliente),
        ]);
    }

    #[OA\Get(
        path: '/api/clientes/{cliente_id}/camisetas',
        summary: 'Camisetas con precio final por cliente',
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(name: 'cliente_id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista con precio_final calculado'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function porCliente($clienteId)
    {
        $cliente   = Cliente::findOrFail($clienteId);
        $camisetas = Camiseta::with('tallas')->get();

        $result = $camisetas->map(function ($c) use ($cliente) {
            $data                 = $c->toArray();
            $data['precio_final'] = $c->getPrecioFinal($cliente);
            return $data;
        });

        return response()->json($result);
    }
}
