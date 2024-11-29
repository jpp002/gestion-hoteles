<?php

namespace Tests\Feature;

use App\Models\Huesped;
use App\Models\Habitacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HuespedTest extends TestCase
{
    use RefreshDatabase;

    public function test_obtener_lista_de_huespedes_paginada()
    {
        Huesped::factory()->count(15)->create();

        $response = $this->getJson('/api/huesped?per_page=10');

        $response->assertStatus(200);
    }

    public function test_obtener_todos_los_huespedes()
    {
        Huesped::factory()->count(5)->create();

        $response = $this->getJson('/api/huesped/all');

        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    public function test_crear_huesped()
    {
        $data = [
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'dniPasaporte' => '12345678X',
        ];

        $response = $this->postJson('/api/huesped', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('huespedes', $data);
    }

    public function test_obtener_huesped_por_id()
    {
        $huesped = Huesped::factory()->create();

        $response = $this->getJson("/api/huesped/{$huesped->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $huesped->id,
                     'nombre' => $huesped->nombre,
                 ]);
    }

    public function test_obtener_huesped_por_id_no_existente()
    {
        $response = $this->getJson('/api/huesped/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'Error' => 'El huesped con ID 999 no existe.',
                 ]);
    }

    public function test_actualizar_huesped()
    {
        $huesped = Huesped::factory()->create();

        $data = [
            'nombre' => 'Nombre Actualizado',
            'apellido' => 'Apellido Actualizado',
            'dniPasaporte' => $huesped->dniPasaporte,
        ];

        $response = $this->putJson("/api/huesped/{$huesped->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('huespedes', $data);
    }

    public function test_eliminar_huesped()
    {
        $huesped = Huesped::factory()->create();

        $response = $this->deleteJson("/api/huesped/{$huesped->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('huespedes', ['id' => $huesped->id]);
    }

    public function test_reservar_habitacion_para_huesped()
    {
        $huesped = Huesped::factory()->create();
        $habitacion = Habitacion::factory()->create();

        $response = $this->postJson("/api/huesped/{$huesped->id}/reservar/{$habitacion->id}");

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'habitacion_id' => $habitacion->id,
                 ]);

        $this->assertDatabaseHas('huespedes', ['habitacion_id' => $habitacion->id]);
    }

    public function test_reservar_habitacion_no_disponible()
    {
        $huesped = Huesped::factory()->create();
        $habitacion = Habitacion::factory()->create(['tipo' => 'simple']);

        $this->postJson("/api/huesped/{$huesped->id}/reservar/{$habitacion->id}");
        
        $huesped2 = Huesped::factory()->create();
        $response = $this->postJson("/api/huesped/{$huesped->id}/reservar/{$habitacion->id}");


        $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'La habitación no está disponible.',
                 ]);
    }

    public function test_registrar_checkout_de_huesped()
    {
        $huesped = Huesped::factory()->create([
            'habitacion_id' => Habitacion::factory()->create()->id,
        ]);

        $response = $this->postJson("/api/huesped/{$huesped->id}/checkout");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Check-out registrado correctamente y habitación liberada.',
                 ]);

        $this->assertDatabaseMissing('huespedes', ['habitacion_id' => $huesped->habitacion_id]);
    }

    public function test_obtener_habitacion_de_huesped()
    {
        $habitacion = Habitacion::factory()->create();
        $huesped = Huesped::factory()->create(['habitacion_id' => $habitacion->id]);

        $response = $this->getJson("/api/huesped/{$huesped->id}/habitacion");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $habitacion->id,
                 ]);
    }
}
