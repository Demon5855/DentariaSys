<?php

namespace App\Models;

use App\Exceptions\StockInsuficienteException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Producto extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'nombre', 'codigo_barras', 'unidad_medida', 'categoria', 'stock_minimo', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class);
    }

    /**
     * Suma solo lotes NO caducados — un lote vencido con cantidad_actual
     * positiva ya no es stock disponible de verdad, aunque siga en la
     * base de datos hasta que alguien registre su merma.
     */
    protected function stockTotal(): Attribute
    {
        return new Attribute(
            get: fn () => $this->lotes
                ->filter(fn (Lote $l) => ! $l->esta_caducado)
                ->sum('cantidad_actual')
        );
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * Bajo mínimo = suma de todos los lotes vigentes por debajo del
     * umbral configurado. Usa una subconsulta correlacionada en el WHERE
     * en vez de withSum()+having(): withSum genera un HAVING sobre una
     * columna de subconsulta, no sobre una agregación real con GROUP BY,
     * y SQLite (a diferencia de MySQL/Postgres) lo rechaza directamente
     * con "HAVING clause on a non-aggregate query". La subconsulta en
     * WHERE evita el problema por completo y funciona igual en los tres.
     */
    public function scopeBajoMinimo(Builder $query): Builder
    {
        return $query->activos()->whereRaw(
            "(select coalesce(sum(cantidad_actual), 0) from lotes where lotes.producto_id = productos.id and lotes.fecha_caducidad >= ?) < stock_minimo",
            [now()->toDateString()]
        );
    }

    /**
     * FEFO (first-expired, first-out): descuenta primero del lote que
     * vence más pronto. Es la práctica estándar de inventario para
     * insumos perecederos — evita que un lote quede "atrás" y venza sin
     * usarse mientras se consumen lotes más nuevos.
     *
     * Bloquea las filas de lotes involucradas (lockForUpdate) para que dos
     * salidas simultáneas del mismo producto no descuenten el mismo stock
     * dos veces — sin esto, dos usuarios registrando tratamientos al
     * mismo tiempo podrían dejar el stock en negativo.
     *
     * IMPORTANTE: excluye lotes ya caducados. Antes de esta corrección, un
     * lote vencido (por ser el que vence "más pronto") era literalmente
     * el primero en la fila de FEFO — se consumía en tratamientos de
     * pacientes reales sin ningún aviso. Un lote vencido con stock
     * restante ya no cuenta ni para el descuento ni para el total
     * disponible; hay que darlo de baja aparte (merma).
     *
     * @return \Illuminate\Support\Collection<int, MovimientoStock>
     *
     * @throws StockInsuficienteException si no hay stock suficiente entre
     *         todos los lotes vigentes (sin contar los caducados).
     */
    public function descontarStock(int $cantidad, array $contexto = []): \Illuminate\Support\Collection
    {
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad a descontar debe ser mayor que cero.');
        }

        return DB::transaction(function () use ($cantidad, $contexto) {
            $lotes = $this->lotes()
                ->where('cantidad_actual', '>', 0)
                ->where('fecha_caducidad', '>=', now()->toDateString())
                ->orderBy('fecha_caducidad')
                ->lockForUpdate()
                ->get();

            $disponible = $lotes->sum('cantidad_actual');

            if ($disponible < $cantidad) {
                throw new StockInsuficienteException($this->nombre, $cantidad, $disponible);
            }

            $restante = $cantidad;
            $movimientos = collect();

            foreach ($lotes as $lote) {
                if ($restante <= 0) {
                    break;
                }

                $aTomar = min($lote->cantidad_actual, $restante);
                $lote->decrement('cantidad_actual', $aTomar);
                $restante -= $aTomar;

                $movimientos->push(MovimientoStock::create([
                    'lote_id' => $lote->id,
                    'usuario_id' => $contexto['usuario_id'] ?? null,
                    'tratamiento_id' => $contexto['tratamiento_id'] ?? null,
                    'tipo' => 'salida',
                    'cantidad' => $aTomar,
                    'motivo' => $contexto['motivo'] ?? null,
                ]));
            }

            return $movimientos;
        });
    }
}