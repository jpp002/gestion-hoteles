<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Huesped\PutRequest;
use App\Http\Requests\Huesped\StoreRequest;
use App\Models\Huesped;
use App\Models\Habitacion;

/**
 * @OA\Tag(name="Huesped", description="Operaciones relacionadas con los huéspedes")
 */
class HuespedController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/huesped",
     *     summary="Obtener lista de huéspedes paginada",
     *     tags={"Huesped"},
     *     @OA\Response(response=200, description="Lista de huéspedes paginada")
     * )
     */
    public function index()
    {
        return response()->json(Huesped::paginate(10));
    }

    /**
     * @OA\Get(
     *     path="/api/huesped/all",
     *     summary="Obtener todos los huéspedes",
     *     tags={"Huesped"},
     *     @OA\Response(response=200, description="Lista completa de huéspedes")
     * )
     */
    public function all()
    {
        return response()->json(Huesped::all());
    }

    /**
     * @OA\Post(
     *     path="/api/huesped",
     *     summary="Crear un nuevo huésped",
     *     tags={"Huesped"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreHuespedRequest")
     *     ),
     *     @OA\Response(response=201, description="Huésped creado correctamente"),
     *     @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function store(StoreRequest $request)
    {
        $huesped = Huesped::create($request->validated());
        return response()->json($huesped, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/huesped/{id}",
     *     summary="Obtener un huésped por ID",
     *     tags={"Huesped"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del huésped", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalles del huésped"),
     *     @OA\Response(response=404, description="Huésped no encontrado")
     * )
     */
    public function show($id)
    {
        $huesped = Huesped::find($id);
        if (!$huesped) {
            return response()->json(['message' => "El huésped con ID {$id} no existe."], 404);
        }
        return response()->json($huesped);
    }

    /**
     * @OA\Put(
     *     path="/api/huesped/{id}",
     *     summary="Actualizar un huésped",
     *     tags={"Huesped"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del huésped", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PutHuespedRequest")
     *     ),
     *     @OA\Response(response=200, description="Huésped actualizado correctamente"),
     *     @OA\Response(response=404, description="Huésped no encontrado")
     * )
     */
    public function update(PutRequest $request, $id)
    {
        $huesped = Huesped::find($id);
        if (!$huesped) {
            return response()->json(['message' => "El huésped con ID {$id} no existe."], 404);
        }
        $huesped->update($request->validated());
        return response()->json($huesped);
    }

    /**
     * @OA\Delete(
     *     path="/api/huesped/{id}",
     *     summary="Eliminar un huésped",
     *     tags={"Huesped"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del huésped", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Huésped eliminado"),
     *     @OA\Response(response=404, description="Huésped no encontrado")
     * )
     */
    public function destroy($id)
    {
        $huesped = Huesped::find($id);
        if (!$huesped) {
            return response()->json(['message' => "El huésped con ID {$id} no existe."], 404);
        }
        $huesped->delete();
        return response()->json(['message' => 'Huésped eliminado correctamente']);
    }

    /**
     * @OA\Get(
     *     path="/api/huesped/{id}/habitacion",
     *     summary="Obtener la habitación de un huésped",
     *     tags={"Huesped"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del huésped", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Habitación del huésped"),
     *     @OA\Response(response=404, description="Huésped no encontrado")
     * )
     */
    public function habitacion($id)
    {
        $huesped = Huesped::find($id);
        if (!$huesped) {
            return response()->json(['message' => "El huésped con ID {$id} no existe."], 404);
        }
        return response()->json($huesped->habitacion);
    }

    /**
     * @OA\Post(
     *     path="/api/huesped/{idHuesped}/reservar/{idHabitacion}",
     *     summary="Reservar una habitación para un huésped",
     *     tags={"Huesped"},
     *     @OA\Parameter(name="idHuesped", in="path", required=true, description="ID del huésped", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="idHabitacion", in="path", required=true, description="ID de la habitación", @OA\Schema(type="integer")),
     *     @OA\Response(response=201, description="Habitación reservada correctamente"),
     *     @OA\Response(response=400, description="Habitación no disponible"),
     *     @OA\Response(response=404, description="Huésped o habitación no encontrado")
     * )
     */
    public function reservarHabitacion($idHuesped, $idHabitacion)
    {
        $huesped = Huesped::find($idHuesped);
        $habitacion = Habitacion::find($idHabitacion);

        if (!$huesped || !$habitacion) {
            return response()->json(['message' => "Huésped o habitación no encontrado."], 404);
        }

        if (!$habitacion->isDisponible()) {
            return response()->json(['message' => "La habitación no está disponible."], 400);
        }

        $huesped->habitacion()->associate($habitacion)->save();
        // Registrar la fecha de check-out
        $huesped->fechaCheckin = now();  // Usar la fecha y hora actuales
        $huesped->save();
        return response()->json($huesped, 201);
    }

    /**
    * @OA\Post(
    *     path="/api/huesped/{idHuesped}/checkout",
    *     summary="Registrar el check-out del huésped y liberar la habitación",
    *     tags={"Huesped"},
    *     @OA\Parameter(name="idHuesped", in="path", required=true, description="ID del huésped", @OA\Schema(type="integer")),
    *     @OA\Response(response=200, description="Check-out registrado correctamente"),
    *     @OA\Response(response=404, description="Huésped no encontrado")
    * )
    */
    public function checkoutHabitacion($idHuesped)
    {
        $huesped = Huesped::find($idHuesped);

        if (!$huesped) {
            return response()->json(['message' => "Huésped no encontrado."], 404);
        }

        $huesped->fechaCheckout = now(); 
        $huesped->save();

        $huesped->habitacion()->dissociate();  
        $huesped->save();  

        return response()->json(['message' => "Check-out registrado correctamente y habitación liberada."], 200);
    }

}
