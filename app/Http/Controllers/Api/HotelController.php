<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HotelNotFoundException;
use App\Exceptions\ServicioNotFoundException;
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
 *     summary="Obtener lista de hoteles paginada con filtros dinámicos",
 *     tags={"Hotel"},
 *     @OA\Parameter(
 *         name="nombre",
 *         in="query",
 *         description="Nombre del hotel para filtrar",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="direccion",
 *         in="query",
 *         description="Dirección del hotel para filtrar",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="telefono",
 *         in="query",
 *         description="Teléfono del hotel para filtrar",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         description="Email del hotel para filtrar",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="sitioWeb",
 *         in="query",
 *         description="Sitio web del hotel para filtrar",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Cantidad de elementos por página",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Número de página",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Lista de hoteles filtrada y paginada")
 * )
 */

    public function index(Request $request)
{
    $query = Hotel::query();

    // Aplicar filtros dinámicos basados en los parámetros de consulta
    $filterableAttributes = ['nombre', 'direccion', 'telefono', 'email', 'sitioWeb'];
    foreach ($request->all() as $key => $value) {
        if (in_array($key, $filterableAttributes) && !empty($value)) {
            $query->where($key, 'like', '%' . $value . '%');
        }
    }

    // Manejo de paginación personalizada
    $perPage = $request->query('per_page', 10); // Por defecto 10 elementos por página
    $hoteles = $query->paginate($perPage);

    if ($hoteles->isEmpty()) {
        return response()->json([
            'mensaje' => 'No se han encontrado hoteles con los filtros seleccionados.',
            'codigo' => 200,
        ], 200);
    }

    return response()->json($hoteles);
}



    /**
     * @OA\Get(
     *     path="/api/hotel/all",
     *     summary="Obtener todos los hoteles",
     *     tags={"Hotel"},
     *     @OA\Response(response=200, description="Lista de todos los hoteles")
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
     *     tags={"Hotel"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreHotelRequest")),
     *     @OA\Response(response=201, description="Hotel creado correctamente")
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
     *     summary="Obtener detalles de un hotel",
     *     tags={"Hotel"},
     *     @OA\Parameter(name="hotel", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalles del hotel"),
     *     @OA\Response(response=404, description="Hotel no encontrado")
     * )
     */
    public function show($idHotel)
    {
        
        $hotel = Hotel::find($idHotel);

        if (!$hotel) {
            throw new HotelNotFoundException($idHotel);
            // return response()->json([
            //     'message' => "El hotel con ID {$idHotel} no existe.",
            // ], 404);
        }

        return response()->json($hotel, 200);
    }


    /**
     * @OA\Put(
     *     path="/api/hotel/{hotel}",
     *     summary="Actualizar un hotel",
     *     tags={"Hotel"},
     *     @OA\Parameter(name="hotel", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/PutHotelRequest")),
     *     @OA\Response(response=200, description="Hotel actualizado correctamente"),
     *     @OA\Response(response=404, description="Hotel no encontrado")
     * )
     */
    public function update(PutRequest $request,  $idHotel)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            throw new HotelNotFoundException($idHotel);
        }

        $hotel->touch();
        $hotel->update($request->validated());
        return response()->json($hotel);
    }

    /**
     * @OA\Delete(
     *     path="/api/hotel/{hotel}",
     *     summary="Eliminar un hotel",
     *     tags={"Hotel"},
     *     @OA\Parameter(name="hotel", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Hotel eliminado correctamente"),
     *     @OA\Response(response=404, description="Hotel no encontrado")
     * )
     */
    public function destroy($idHotel)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            throw new HotelNotFoundException($idHotel);
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
     *     summary="Obtener las habitaciones de un hotel",
     *     tags={"Hotel"},
     *     @OA\Parameter(name="hotel", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lista de habitaciones")
     * )
     */
    public function habitaciones($idHotel)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            throw new HotelNotFoundException($idHotel);
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
     *     @OA\Parameter(name="hotel", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="servicio", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Servicio asociado correctamente"),
     *     @OA\Response(response=400, description="Error en la asociación")
     * )
     */
    public function addServicio($idHotel, $idServicio)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            throw new HotelNotFoundException($idHotel);
        }

        $servicio = servicio::find($idServicio);
        if (!$servicio) {
            throw new ServicioNotFoundException($idServicio);
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
     *     @OA\Parameter(name="hotel", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="servicio", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Servicio desasociado correctamente"),
     *     @OA\Response(response=400, description="Error en la desasociación")
     * )
     */
    public function removeServicio($idHotel, $idServicio)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            throw new HotelNotFoundException($idHotel);
        }

        $servicio = servicio::find($idServicio);
        if (!$servicio) {
            throw new ServicioNotFoundException($idServicio);
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
     *     summary="Obtener los servicios de un hotel",
     *     tags={"Hotel"},
     *     @OA\Parameter(name="hotel", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lista de servicios")
     * )
     */
    public function servicios($idHotel)
    {
        $hotel = Hotel::find($idHotel);
        if (!$hotel) {
            throw new HotelNotFoundException($idHotel);
        }

        $servicios = $hotel->servicios;

        if ($servicios->isEmpty()) {
            return response()->json(["message" => "Este hotel no tiene servicios"], 404);
        }
        return response()->json($servicios, 200);
    }
}
