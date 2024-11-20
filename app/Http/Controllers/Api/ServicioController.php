<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Servicio\PutRequest;
use App\Http\Requests\Servicio\StoreRequest;
use App\Models\Servicio;
use Illuminate\Http\Request;

/**
 *
 * @OA\Tag(
 *     name="Servicio",
 *     description="Operaciones relacionadas con los servicios"
 * )
 */

class ServicioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/servicio",
     *     summary="Obtener lista de servicios paginada",
     *     tags={"Servicio"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de servicios paginada"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Servicio::paginate(10));
    }

    /**
     * @OA\Get(
     *     path="/api/servicio/all",
     *     summary="Obtener todos los servicios",
     *     tags={"Servicio"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista completa de servicios"
     *     )
     * )
     */
    public function all()
    {
        return response()->json(Servicio::get());
    }

    /**
     * @OA\Post(
     *     path="/api/servicio",
     *     summary="Crear un nuevo servicio",
     *     description="Crea un nuevo servicio con los datos proporcionados",
     *     tags={"Servicio"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para crear un servicio",
     *         @OA\JsonContent(ref="#/components/schemas/StoreServicioRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Servicio creado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Servicio")
     *     )
     * )
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        // Crear el hotel manualmente
        $servicio = new Servicio($data);
        $servicio->timestamps = false; // Evita la actualización automática de timestamps
        $servicio->created_at = now(); // Establece created_at manualmente
        $servicio->updated_at = null; // No queremos modificar updated_at en creación
        $servicio->save();

        return response()->json($servicio, 201);
        //return response()->json(Servicio::create($request->validated()));
    }

    /**
     * @OA\Get(
     *     path="/api/servicio/{servicio}",
     *     summary="Obtener un servicio",
     *     description="Devuelve los detalles de un servicio específico.",
     *     tags={"Servicio"},
     *     @OA\Parameter(
     *         name="servicio",
     *         in="path",
     *         required=true,
     *         description="ID del servicio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del servicio",
     *         @OA\JsonContent(ref="#/components/schemas/Servicio")
     *     )
     * )
     */
    public function show($idServicio)
    {
        $servicio = Servicio::find($idServicio);

        if (!$servicio) {
            return response()->json([
                'message' => "La habitación con ID {$idServicio} no existe.",
            ], 404);
        }
        
        return response()->json($servicio);
    }

    /**
     * @OA\Put(
     *     path="/api/servicio/{servicio}",
     *     summary="Actualizar datos de un servicio",
     *     description="Actualiza los datos de un servicio específico.",
     *     tags={"Servicio"},
     *     @OA\Parameter(
     *         name="servicio",
     *         in="path",
     *         required=true,
     *         description="ID del servicio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PutServicioRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Servicio actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Servicio")
     *     )
     * )
     */
    public function update(PutRequest $request, $idServicio)
    {
        $servicio = Servicio::find($idServicio);

        if (!$servicio) {
            return response()->json([
                'message' => "La habitación con ID {$idServicio} no existe.",
            ], 404);
        }
        $servicio->updated_at = now();
        $servicio->update($request->validated());
        return response()->json($servicio);
    }

    /**
     * @OA\Delete(
     *     path="/api/servicio/{servicio}",
     *     summary="Eliminar un servicio",
     *     description="Elimina un servicio específico por ID.",
     *     tags={"Servicio"},
     *     @OA\Parameter(
     *         name="servicio",
     *         in="path",
     *         required=true,
     *         description="ID del servicio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Servicio eliminado",
     *         @OA\JsonContent(
     *             type="string",
     *             example="ok"
     *         )
     *     )
     * )
     */
    public function destroy($idServicio)
    {
        $servicio = Servicio::find($idServicio);

        if (!$servicio) {
            return response()->json([
                'message' => "La habitación con ID {$idServicio} no existe.",
            ], 404);
        }

        $servicio->delete();
        return response()->json("ok");
    }

    /**
     * @OA\Get(
     *     path="/api/servicio/{servicio}/hoteles",
     *     summary="Obtener los hoteles asociados a un servicio",
     *     description="Devuelve la lista de hoteles asociados a un servicio específico.",
     *     tags={"Servicio"},
     *     @OA\Parameter(
     *         name="servicio",
     *         in="path",
     *         required=true,
     *         description="ID del servicio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de hoteles asociados al servicio",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 ref="#/components/schemas/Hotel"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Servicio no encontrado",
     *         @OA\JsonContent(
     *             type="string",
     *             example="Servicio no encontrado"
     *         )
     *     )
     * )
     */
    public function hoteles($idServicio) {
        $servicio = Servicio::find($idServicio);

        if (!$servicio) {
            return response()->json([
                'message' => "La habitación con ID {$idServicio} no existe.",
            ], 404);
        }

        $hoteles = $servicio->hoteles;
        if($hoteles->isEmpty()){
            return response()->json(["message" => "Este servicio no esta asociado a ningun hotel"], 404);
        }

        return response()->json($hoteles);
    }

}
