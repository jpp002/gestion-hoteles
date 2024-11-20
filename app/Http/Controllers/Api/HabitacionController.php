<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Habitacion\PutRequest;
use App\Http\Requests\Habitacion\StoreRequest;
use App\Models\Habitacion;
use App\Models\Hotel;
use Illuminate\Http\Request;

/**
 * 
 * @OA\Tag(
 *     name="Habitación",
 *     description="Operaciones relacionadas con las habitaciones"
 * )
 */

class HabitacionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/habitacion",
     *     summary="Obtener lista de habitaciones paginada",
     *     tags={"Habitación"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de habitaciones"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Habitacion::paginate(10));
    }

    /**
     * @OA\Get(
     *     path="/api/habitacion/all",
     *     summary="Obtener todas las habitaciones",
     *     tags={"Habitación"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de todas las habitaciones"
     *     )
     * )
     */
    public function all()
    {
        return response()->json(Habitacion::get());
    }

    /**
     * @OA\Post(
     *     path="/api/habitacion",
     *     summary="Crear una nueva habitación",
     *     description="Crea una nueva habitación con los datos proporcionados",
     *     tags={"Habitación"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para crear una habitación",
     *         @OA\JsonContent(ref="#/components/schemas/StoreHabitacionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Habitación creada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Habitacion")
     *     )
     * )
     */
    public function store(StoreRequest $request)
    {
        $hotel = Hotel::find($request->hotel_id);

        if (!$hotel) {
            // Si el hotel no existe, devolver un error 404
            return response()->json(['error' => "El hotel con id {$request->hotel_id} no existe"], 404);
        }

        $data = $request->validated();

        
        $habitacion = new Habitacion($data);
        $habitacion->timestamps = false; // Evita la actualización automática de timestamps
        $habitacion->created_at = now(); // Establece created_at manualmente
        $habitacion->updated_at = null; // No queremos modificar updated_at en creación
        $habitacion->save();

        return response()->json($habitacion, 201);

        //return response()->json(Habitacion::create($request->validated()));
    }

    /**
     * @OA\Get(
     *     path="/api/habitacion/{habitacion}",
     *     summary="Obtener detalles de una habitación",
     *     description="Devuelve los detalles de una habitación específica",
     *     tags={"Habitación"},
     *     @OA\Parameter(
     *         name="habitacion",
     *         in="path",
     *         required=true,
     *         description="ID de la habitación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la habitación",
     *         @OA\JsonContent(ref="#/components/schemas/Habitacion")
     *     )
     * )
     */
    public function show($idHabitacion)
    {
        $habitacion = Habitacion::find($idHabitacion);

        if (!$habitacion) {
            return response()->json([
                'message' => "La habitación con idHabitacion {$idHabitacion} no existe.",
            ], 404);
        }

        return response()->json($habitacion, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/habitacion/{habitacion}",
     *     summary="Actualizar una habitación",
     *     description="Actualiza los datos de una habitación específica",
     *     tags={"Habitación"},
     *     @OA\Parameter(
     *         name="habitacion",
     *         in="path",
     *         required=true,
     *         description="ID de la habitación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para actualizar una habitación",
     *         @OA\JsonContent(ref="#/components/schemas/PutHabitacionRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habitación actualizada",
     *         @OA\JsonContent(ref="#/components/schemas/Habitacion")
     *     )
     * )
     */
    public function update(PutRequest $request, $idHabitacion)
    {
        $habitacion = Habitacion::find($idHabitacion);

        if (!$habitacion) {
            return response()->json([
                'message' => "La habitación con ID {$idHabitacion} no existe.",
            ], 404);
        }

        $habitacion->created_at = now();
        $habitacion->update($request->validated());
        return response()->json($habitacion);
    }

    /**
     * @OA\Delete(
     *     path="/api/habitacion/{habitacion}",
     *     summary="Eliminar una habitación",
     *     description="Elimina una habitación específica por su ID",
     *     tags={"Habitación"},
     *     @OA\Parameter(
     *         name="habitacion",
     *         in="path",
     *         required=true,
     *         description="ID de la habitación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habitación eliminada",
     *         @OA\JsonContent(type="string", example="ok")
     *     )
     * )
     */
    public function destroy($idHabitacion)
    {
        $habitacion = Habitacion::find($idHabitacion);

        if (!$habitacion) {
            return response()->json([
                'message' => "La habitación con ID {$idHabitacion} no existe.",
            ], 404);
        }
        
        try {
            $habitacion->delete();
            return response()->json(["message" => "Habitacion eliminada correctamente"], 200);
        } catch (\Exception $e) {
            return response()->json(["error" => "No se pudo eliminar la habitacion"], 400);
        }
    }

    /**
 * @OA\Get(
 *     path="/api/habitacion/{habitacion}/hotel",
 *     summary="Obtener el hotel asociado a una habitación",
 *     description="Devuelve el hotel asociado a una habitación específica.",
 *     tags={"Habitación"},
 *     @OA\Parameter(
 *         name="habitacion",
 *         in="path",
 *         required=true,
 *         description="ID de la habitación",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalles del hotel asociado a la habitación",
 *         @OA\JsonContent(ref="#/components/schemas/Hotel")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Habitación no encontrada",
 *         @OA\JsonContent(
 *             type="string",
 *             example="Habitación no encontrada"
 *         )
 *     )
 * )
 */
    public function hotel($idHabitacion) {
        $habitacion = Habitacion::find($idHabitacion);

        if (!$habitacion) {
            return response()->json([
                'message' => "La habitación con ID {$idHabitacion} no existe.",
            ], 404);
        }

        $hotel = $habitacion->hotel;
        return response()->json($hotel);
    }

    /**
     * @OA\Get(
     *     path="/api/habitacion/{habitacion}/huespedes",
     *     summary="Obtener los huéspedes asociados a una habitación",
     *     description="Devuelve la lista de huéspedes asociados a una habitación específica.",
     *     tags={"Habitación"},
     *     @OA\Parameter(
     *         name="habitacion",
     *         in="path",
     *         required=true,
     *         description="ID de la habitación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de huéspedes asociados a la habitación",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 ref="#/components/schemas/Huesped"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitación no encontrada",
     *         @OA\JsonContent(
     *             type="string",
     *             example="Habitación no encontrada"
     *         )
     *     )
     * )
     */
    public function huespedes($idHabitacion) {
        $habitacion = Habitacion::find($idHabitacion);

        if (!$habitacion) {
            return response()->json([
                'message' => "La habitación con ID {$idHabitacion} no existe.",
            ], 404);
        }
        $huespedes = $habitacion->huespedes;
        if($huespedes->isEmpty()){
            return response()->json(["message" => "Esta habitacion no tiene huespedes"], 404);
        }
        return response()->json($huespedes);
    }


}
