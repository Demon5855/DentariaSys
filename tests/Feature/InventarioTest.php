<?php

namespace Tests\Feature;

use App\Exceptions\StockInsuficienteException;
use App\Models\Consulta;
use App\Models\HistoriaClinica;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function recepcion(): User
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('recepcion');

        return $usuario;
    }

    private function odontologo(): User
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('odontologo');

        return $usuario;
    }

    // ── FIFO/FEFO: el corazón de la fase ──────────────────────────────

    public function test_descuenta_del_lote_que_vence_primero(): void
    {
        $producto = Producto::factory()->create();
        $loteLejano = Lote::factory()->create([
            'producto_id' => $producto->id,
            'fecha_caducidad' => now()->addYear(),
            'cantidad_inicial' => 50,
            'cantidad_actual' => 50,
        ]);
        $loteProximo = Lote::factory()->create([
            'producto_id' => $producto->id,
            'fecha_caducidad' => now()->addMonth(),
            'cantidad_inicial' => 50,
            'cantidad_actual' => 50,
        ]);

        $producto->descontarStock(10);

        $this->assertSame(40, $loteProximo->fresh()->cantidad_actual, 'debe descontar del lote que vence antes');
        $this->assertSame(50, $loteLejano->fresh()->cantidad_actual, 'el lote lejano no debe tocarse todavía');
    }

    public function test_reparte_el_descuento_entre_varios_lotes_cuando_uno_no_alcanza(): void
    {
        $producto = Producto::factory()->create();
        $loteA = Lote::factory()->create([
            'producto_id' => $producto->id,
            'fecha_caducidad' => now()->addMonth(),
            'cantidad_inicial' => 5,
            'cantidad_actual' => 5, // no alcanza solo
        ]);
        $loteB = Lote::factory()->create([
            'producto_id' => $producto->id,
            'fecha_caducidad' => now()->addMonths(2),
            'cantidad_inicial' => 20,
            'cantidad_actual' => 20,
        ]);

        $movimientos = $producto->descontarStock(8); // 5 del A (se agota) + 3 del B

        $this->assertSame(0, $loteA->fresh()->cantidad_actual);
        $this->assertSame(17, $loteB->fresh()->cantidad_actual);
        $this->assertCount(2, $movimientos, 'debe generar un movimiento por cada lote tocado');
        $this->assertSame(5, $movimientos->firstWhere('lote_id', $loteA->id)->cantidad);
        $this->assertSame(3, $movimientos->firstWhere('lote_id', $loteB->id)->cantidad);
    }

    public function test_no_toca_lotes_agotados_ni_los_cuenta_para_el_disponible(): void
    {
        $producto = Producto::factory()->create();
        Lote::factory()->create([
            'producto_id' => $producto->id,
            'fecha_caducidad' => now()->addWeek(), // vence primero, pero ya está en 0
            'cantidad_inicial' => 30,
            'cantidad_actual' => 0,
        ]);
        $loteConStock = Lote::factory()->create([
            'producto_id' => $producto->id,
            'fecha_caducidad' => now()->addMonth(),
            'cantidad_inicial' => 30,
            'cantidad_actual' => 30,
        ]);

        $movimientos = $producto->descontarStock(10);

        $this->assertCount(1, $movimientos);
        $this->assertSame($loteConStock->id, $movimientos->first()->lote_id);
        $this->assertSame(20, $loteConStock->fresh()->cantidad_actual);
    }

    public function test_lanza_excepcion_si_no_hay_stock_suficiente_entre_todos_los_lotes(): void
    {
        $producto = Producto::factory()->create();
        Lote::factory()->create(['producto_id' => $producto->id, 'cantidad_inicial' => 5, 'cantidad_actual' => 5]);
        Lote::factory()->create(['producto_id' => $producto->id, 'cantidad_inicial' => 3, 'cantidad_actual' => 3]);

        $this->expectException(StockInsuficienteException::class);

        $producto->descontarStock(20); // hay 8 en total, piden 20
    }

    public function test_una_excepcion_de_stock_no_deja_movimientos_a_medias(): void
    {
        $producto = Producto::factory()->create();
        $lote = Lote::factory()->create(['producto_id' => $producto->id, 'cantidad_inicial' => 5, 'cantidad_actual' => 5]);

        try {
            $producto->descontarStock(100);
        } catch (StockInsuficienteException) {
            // esperado
        }

        $this->assertSame(5, $lote->fresh()->cantidad_actual, 'el lote no debe haberse tocado si la operación falló');
        $this->assertDatabaseCount('movimientos_stock', 0);
    }

    public function test_rechaza_cantidad_cero_o_negativa(): void
    {
        $producto = Producto::factory()->create();
        Lote::factory()->create(['producto_id' => $producto->id, 'cantidad_actual' => 10]);

        $this->expectException(\InvalidArgumentException::class);
        $producto->descontarStock(0);
    }

    // ── Alertas ────────────────────────────────────────────────────────

    public function test_bajo_minimo_detecta_productos_por_debajo_del_umbral(): void
    {
        $bajo = Producto::factory()->create(['stock_minimo' => 20]);
        Lote::factory()->create(['producto_id' => $bajo->id, 'cantidad_actual' => 5]);

        $normal = Producto::factory()->create(['stock_minimo' => 20]);
        Lote::factory()->create(['producto_id' => $normal->id, 'cantidad_actual' => 50]);

        $resultado = Producto::bajoMinimo()->pluck('id');

        $this->assertTrue($resultado->contains($bajo->id));
        $this->assertFalse($resultado->contains($normal->id));
    }

    public function test_por_vencer_detecta_lotes_dentro_del_rango_de_dias(): void
    {
        $producto = Producto::factory()->create();
        $proximo = Lote::factory()->create([
            'producto_id' => $producto->id,
            'fecha_caducidad' => now()->addDays(10),
            'cantidad_actual' => 5,
        ]);
        Lote::factory()->create([
            'producto_id' => $producto->id,
            'fecha_caducidad' => now()->addYear(),
            'cantidad_actual' => 5,
        ]);

        $resultado = Lote::porVencer(30)->pluck('id');

        $this->assertTrue($resultado->contains($proximo->id));
        $this->assertCount(1, $resultado);
    }

    // ── Salida manual (HTTP) ─────────────────────────────────────────

    public function test_recepcion_registra_una_salida_manual(): void
    {
        $producto = Producto::factory()->create();
        Lote::factory()->create(['producto_id' => $producto->id, 'fecha_caducidad' => now()->addMonth(), 'cantidad_actual' => 20]);

        $response = $this->actingAs($this->recepcion())->post(route('movimientos.guardar-salida'), [
            'producto_id' => $producto->id,
            'cantidad' => 5,
            'motivo' => 'Uso general',
        ]);

        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('movimientos_stock', ['tipo' => 'salida', 'cantidad' => 5]);
    }

    public function test_salida_manual_sin_stock_suficiente_regresa_con_error(): void
    {
        $producto = Producto::factory()->create();
        Lote::factory()->create(['producto_id' => $producto->id, 'cantidad_actual' => 2]);

        $response = $this->actingAs($this->recepcion())->post(route('movimientos.guardar-salida'), [
            'producto_id' => $producto->id,
            'cantidad' => 10,
            'motivo' => 'Uso general',
        ]);

        $response->assertSessionHasErrors('cantidad');
    }

    public function test_odontologo_no_puede_gestionar_inventario(): void
    {
        $this->actingAs($this->odontologo())
            ->get(route('productos.create'))
            ->assertForbidden();
    }

    // ── Integración con tratamientos ───────────────────────────────────

    public function test_registrar_tratamiento_con_insumos_descuenta_stock_automaticamente(): void
    {
        $producto = Producto::factory()->create();
        $lote = Lote::factory()->create(['producto_id' => $producto->id, 'fecha_caducidad' => now()->addMonth(), 'cantidad_actual' => 10]);
        $historiaClinica = HistoriaClinica::factory()->create();

        $this->actingAs($this->odontologo())->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Control',
            'tratamientos' => [
                [
                    'fecha' => now()->toDateString(),
                    'procedimiento' => 'Obturación',
                    'estado' => 'alta',
                    'productos' => [
                        ['producto_id' => $producto->id, 'cantidad' => 3],
                    ],
                ],
            ],
        ]);

        $this->assertSame(7, $lote->fresh()->cantidad_actual);

        $consulta = $historiaClinica->consultas()->first();
        $tratamiento = $consulta->tratamientos->first();
        $this->assertTrue($tratamiento->productos->contains($producto));
        $this->assertSame(3, $tratamiento->productos->first()->pivot->cantidad);
        $this->assertDatabaseHas('movimientos_stock', ['tratamiento_id' => $tratamiento->id, 'cantidad' => 3]);
    }

    public function test_tratamiento_con_insumo_sin_stock_suficiente_no_crea_nada(): void
    {
        $producto = Producto::factory()->create();
        Lote::factory()->create(['producto_id' => $producto->id, 'cantidad_actual' => 1]);
        $historiaClinica = HistoriaClinica::factory()->create();

        $response = $this->actingAs($this->odontologo())->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Control',
            'tratamientos' => [
                [
                    'fecha' => now()->toDateString(),
                    'procedimiento' => 'Obturación',
                    'estado' => 'alta',
                    'productos' => [
                        ['producto_id' => $producto->id, 'cantidad' => 50], // no hay stock
                    ],
                ],
            ],
        ]);

        $response->assertSessionHasErrors('tratamientos');

        // Nada de la consulta debe haberse guardado — todo o nada.
        $this->assertDatabaseCount('consultas', 0);
        $this->assertDatabaseCount('tratamientos', 0);
        $this->assertDatabaseCount('movimientos_stock', 0);
    }
}
