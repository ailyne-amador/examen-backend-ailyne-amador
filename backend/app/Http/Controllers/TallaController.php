<?php

namespace App\Http\Controllers;

use App\Models\Talla;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TallaController extends Controller
{
    #[OA\Get(
        path: '/api/tallas',
        summary: 'Listar todas las tallas',
        tags: ['Tallas'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de tallas',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'nombre', type: 'string', example: 'M'),
                        ]
                    )
                )
            )
        ]
    )]
    public function index()
    {
        return response()->json(Talla::all());
    }

    #[OA\Post(
        path: '/api/tallas',
        summary: 'Crear una talla',
        tags: ['Tallas'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'XXXL'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Talla creada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 7),
                        new OA\Property(property: 'nombre', type: 'string', example: 'XXXL'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Nombre de talla duplicado o inválido'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:tallas',
        ]);
        return response()->json(Talla::create($validated), 201);
    }

    #[OA\Get(
        path: '/api/tallas/{id}',
        summary: 'Ver una talla por ID',
        tags: ['Tallas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Talla encontrada con camisetas asociadas',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'nombre', type: 'string', example: 'S'),
                        new OA\Property(
                            property: 'camisetas',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'titulo', type: 'string', example: 'Camiseta Local 2025'),
                                    new OA\Property(property: 'codigo_producto', type: 'string', example: 'SCL2025L'),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Talla no encontrada'),
        ]
    )]
    public function show($id)
    {
        return response()->json(Talla::with('camisetas')->findOrFail($id));
    }

    #[OA\Put(
        path: '/api/tallas/{id}',
        summary: 'Actualizar una talla',
        tags: ['Tallas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'XXXL'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Talla actualizada'),
            new OA\Response(response: 404, description: 'Talla no encontrada'),
            new OA\Response(response: 422, description: 'Nombre de talla duplicado'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $talla = Talla::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|unique:tallas,nombre,' . $id,
        ]);
        $talla->update($validated);
        return response()->json($talla);
    }

    #[OA\Delete(
        path: '/api/tallas/{id}',
        summary: 'Eliminar una talla',
        tags: ['Tallas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Talla eliminada'),
            new OA\Response(response: 404, description: 'Talla no encontrada'),
            new OA\Response(response: 422, description: 'La talla tiene camisetas asociadas y no puede eliminarse'),
        ]
    )]
    public function destroy($id)
    {
        $talla = Talla::findOrFail($id);
        if ($talla->camisetas()->count() > 0) {
            return response()->json(['error' => 'No se puede eliminar una talla con camisetas asociadas'], 422);
        }
        $talla->delete();
        return response()->json(['message' => 'Talla eliminada']);
    }
}
