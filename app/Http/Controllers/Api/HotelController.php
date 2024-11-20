<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\PutRequest;
use App\Http\Requests\Hotel\StoreRequest;
use App\Models\Habitacion;
use App\Models\Hotel;
use App\Models\Servicio;
use Illuminate\Http\Request;

/*
 * 
 * @OA\Tag(
 *     name="Hotel",
 *     description="Operaciones relacionadas con los hoteles"
 * )
 */

class HotelController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/hotel",
     *     summary="Obtener lista de hoteles paginada",
     *     tags={"Hotel"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de hoteles"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Hotel::paginate(10));
    }

    /**
     * @OA\Get(
     *     path="/api/hotel/all",
     *     summary="Obtener todos los hoteles",
     *     tags={"Hotel"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de todas los hoteles"
     *     )
     * )
     */
    public function all()
    {
        return response()->json(Hotel::get());
    }

    /**
     * @OA\Post(
     *     path="/api/hotel",
     *     summary="Crear un nuevo hotel",
     *     description="Crea un nuevo hotel con los datos proporcionados",
     *     tags={"Hotel"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para crear un hotel",
     *         @OA\JsonContent(ref="#/components/schemas/StoreHotelRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Hotel creado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Hotel")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties=@OA\Property(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        // Crear el hotel manualmente
        $hotel = new Hotel($data);
        $hotel->timestamps = false; // Evita la actualización automática de timestamps
        $hotel->created_at = now(); // Establece created_at manualmente
        $hotel->updated_at = null; // No queremos modificar updated_at en creación
        $hotel->save();

        return response()->json($hotel, 201);
    }


    /**
     * @OA\Get(
     *     path="/api/hotel/{hotel}",
     *     summary="Obtener hotel",
     *     description="Devuelve los detalles de un hotel específico.",
     *     tags={"Hotel"},
     *     @OA\Parameter(
     *         name="hotel",
     *         in="path",
     *         required=true,
     *         description="ID del hotel",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del hotel",
     *         @OA\JsonContent(ref="#/components/schemas/Hotel")
     *     )
     * )
     */
    public function show($idHotel)
    {
        $hotel = Hotel::find($idHotel);

        if (!$hotel) {
            return response()->json([
                'message' => "El hotel con ID {$idHotel} no existe.",
            ], 404);
        }

        return response()->json($hotel, 200);
    }


    /**
     * @OA\Put(
     *     path="/api/hotel/{hotel}",
     *     summary="Editar hotel",
     *     description="Actualiza los datos de un hotel específico.",
     *     tags={"Hotel"},
     *     @OA\Parameter(
     *         name="hotel",
     *         in="path",
     *         required=true,
     *         description="ID del hotel",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PutHotelRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hotel actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Hotel")
     *     )
     * )
     */
    public function update(PutRequest $request,  $idHotel)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            return response()->json([
                'message' => "El hotel con ID {$idHotel} no existe.",
            ], 404);
        }

        $hotel->touch();
        $hotel->update($request->validated());
        return response()->json($hotel);
    }

    /**
     * @OA\Delete(
     *     path="/api/hotel/{hotel}",
     *     summary="Eliminar un hotel",
     *     description="Elimina un hotel específico por ID.",
     *     tags={"Hotel"},
     *     @OA\Parameter(
     *         name="hotel",
     *         in="path",
     *         required=true,
     *         description="ID del hotel",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hotel eliminado",
     *         @OA\JsonContent(
     *             type="string",
     *             example="ok"
     *         )
     *     )
     * )
     */
    public function destroy($idHotel)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            return response()->json([
                'message' => "El hotel con ID {$idHotel} no existe.",
            ], 404);
        }

        try {
            $hotel->delete();
            return response()->json(["message" => "Hotel eliminado correctamente"], 200);
        } catch (\Exception $e) {
            return response()->json(["error" => "No se pudo eliminar el hotel"], 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/hotel/{hotel}/habitaciones",
     *     summary="Obtener habitaciones de un hotel",
     *     description="Obtiene todas las habitaciones de un hotel.",
     *     tags={"Hotel"},
     *     @OA\Parameter(
     *         name="hotel",
     *         in="path",
     *         required=true,
     *         description="ID del hotel",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de habitaciones del hotel",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Habitacion"))
     *     )
     * )
     */
    public function habitaciones($idHotel) 
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            return response()->json([
                'message' => "El hotel con ID {$idHotel} no existe.",
            ], 404);
        }
        
        $habitaciones = $hotel->habitaciones;

        if ($habitaciones->isEmpty()) {
            return response()->json(["message" => "Este hotel no tiene habitaciones"], 404);
        }

        return response()->json($habitaciones, 200);
    }

        /**
     * @OA\Post(
     *     path="/api/hotel/{hotel}/servicio/{servicio}",
     *     summary="Asociar un servicio a un hotel",
     *     tags={"Hotel"},
     *     @OA\Parameter(
     *         name="hotel",
     *         in="path",
     *         required=true,
     *         description="ID del hotel al que se asociará el servicio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="servicio",
     *         in="path",
     *         required=true,
     *         description="ID del servicio a asociar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Servicio asociado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Servicio asociado correctamente"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El servicio ya está asociado a este hotel",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="El servicio ya está asociado a este hotel"
     *             )
     *         )
     *     )
     * )
     */
    public function addServicio($idHotel, $idServicio)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            return response()->json([
                'message' => "El hotel con ID {$idHotel} no existe.",
            ], 404);
        }

        $servicio = servicio::find($idServicio);
        if (!$servicio) {
            return response()->json([
                'message' => "El Servicio con ID {$idServicio} no existe.",
            ], 404);
        }

        if ($hotel->servicios->contains($servicio->id)) {
            return response()->json(['message' => 'El servicio ya está asociado a este hotel'], 400);
        }
    
        $hotel->servicios()->attach($servicio->id);
    
        return response()->json(['message' => 'Servicio asociado correctamente'], 200);
    }
     


    /**
     * @OA\Delete(
     *     path="/api/hotel/{hotel}/servicio/{servicio}",
     *     summary="Desasociar un servicio de un hotel",
     *     tags={"Hotel"},
     *     @OA\Parameter(
     *         name="hotel",
     *         in="path",
     *         required=true,
     *         description="ID del hotel del que se desasociará el servicio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="servicio",
     *         in="path",
     *         required=true,
     *         description="ID del servicio a desasociar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Servicio desasociado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Servicio desasociado correctamente"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El servicio no está asociado a este hotel",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="El servicio no está asociado a este hotel"
     *             )
     *         )
     *     )
     * )
     */
    public function removeServicio($idHotel, $idServicio)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            return response()->json([
                'message' => "El hotel con ID {$idHotel} no existe.",
            ], 404);
        }

        $servicio = servicio::find($idServicio);
        if (!$servicio) {
            return response()->json([
                'message' => "El Servicio con ID {$idServicio} no existe.",
            ], 404);
        }


        if (!$hotel->servicios->contains($servicio->id)) {
            return response()->json(['message' => 'El servicio no está asociado a este hotel'], 400);
        }

        $hotel->servicios()->detach($servicio->id);

        return response()->json(['message' => 'Servicio desasociado correctamente'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/hotel/{hotel}/servicios",
     *     summary="Obtener servicios de un hotel",
     *     description="Obtiene todos los servicios asociados a un hotel.",
     *     tags={"Hotel"},
     *     @OA\Parameter(
     *         name="hotel",
     *         in="path",
     *         required=true,
     *         description="ID del hotel",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de servicios del hotel",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Servicio"))
     *     )
     * )
     */
    public function servicios($idHotel)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            return response()->json([
                'message' => "El hotel con ID {$idHotel} no existe.",
            ], 404);
        }

        $servicios = $hotel->servicios;

        if($servicios->isEmpty()){
            return response()->json(["message" => "Este hotel no tiene servicios"], 404);
        }
        return response()->json($servicios, 200);
    }

}
