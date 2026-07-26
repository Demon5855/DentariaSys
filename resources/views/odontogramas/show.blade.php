<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Odontograma {{ ucfirst($odontograma->tipo) }} — {{ $odontograma->fecha->format('d/m/Y') }}
            · <span class="text-indigo-600">{{ $odontograma->historiaClinica->paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <style>
        .odonto-lienzo * { box-sizing: border-box; }
        .odonto-arcada { display: flex; justify-content: center; gap: 26px; }
        .odonto-hemi { display: flex; gap: 3px; }
        .odonto-fila + .odonto-fila { margin-top: 14px; }
        .odonto-eje { display: flex; align-items: center; gap: 10px; margin: 16px 0; color: #9aa3ae; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; }
        .odonto-eje::before, .odonto-eje::after { content: ""; flex: 1; height: 1px; background: #d3d8de; }
        .odonto-pieza { display: flex; flex-direction: column; align-items: center; gap: 2px; }
        .odonto-pieza .numero { font-size: 10.5px; color: #5b6470; }
        .odonto-indices .celda { min-width: 42px; padding: 5px 8px; text-align: center; background: #f6f8fa; border: 1px solid #d3d8de; }
        .odonto-indices .celda.total { background: #16191d; color: #fff; border-color: #16191d; }
    </style>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-wrap justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Dentición</p>
                    <p class="font-medium">{{ ucfirst($odontograma->denticion) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Firmado por</p>
                    <p class="font-medium">{{ $odontograma->odontologo->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Firmado el</p>
                    <p class="font-medium">{{ $odontograma->firmado_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="text-right">
                    <span class="bg-gray-800 text-white text-xs font-medium px-2.5 py-1 rounded-full">🔒 Bloqueado — solo lectura</span>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6" x-data="odontogramaVisor(@js($condicionesIndexadas), @js($hallazgosPorPieza))" x-cloak>
                <div class="odonto-lienzo">

                    <div class="odonto-fila" x-show="'{{ $odontograma->denticion }}' !== 'temporal'">
                        <div class="odonto-arcada">
                            <div class="odonto-hemi"><template x-for="p in permanentes(1)" :key="p"><div x-html="dibujar(p)"></div></template></div>
                            <div class="odonto-hemi"><template x-for="p in permanentes(2)" :key="p"><div x-html="dibujar(p)"></div></template></div>
                        </div>
                    </div>

                    <div class="odonto-fila" x-show="'{{ $odontograma->denticion }}' !== 'permanente'">
                        <div class="odonto-arcada">
                            <div class="odonto-hemi"><template x-for="p in temporales(5)" :key="p"><div x-html="dibujar(p)"></div></template></div>
                            <div class="odonto-hemi"><template x-for="p in temporales(6)" :key="p"><div x-html="dibujar(p)"></div></template></div>
                        </div>
                    </div>

                    <p class="odonto-eje">Línea media</p>

                    <div class="odonto-fila" x-show="'{{ $odontograma->denticion }}' !== 'permanente'">
                        <div class="odonto-arcada">
                            <div class="odonto-hemi"><template x-for="p in temporales(8)" :key="p"><div x-html="dibujar(p)"></div></template></div>
                            <div class="odonto-hemi"><template x-for="p in temporales(7)" :key="p"><div x-html="dibujar(p)"></div></template></div>
                        </div>
                    </div>

                    <div class="odonto-fila" x-show="'{{ $odontograma->denticion }}' !== 'temporal'">
                        <div class="odonto-arcada">
                            <div class="odonto-hemi"><template x-for="p in permanentes(4)" :key="p"><div x-html="dibujar(p)"></div></template></div>
                            <div class="odonto-hemi"><template x-for="p in permanentes(3)" :key="p"><div x-html="dibujar(p)"></div></template></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-3">Índices CPO-D / ceo-d (congelados al firmar)</p>
                <div class="flex gap-8 flex-wrap odonto-indices">
                    <div class="flex">
                        <div class="celda"><b>{{ $odontograma->cpod_c }}</b><br><span class="text-xs">C</span></div>
                        <div class="celda"><b>{{ $odontograma->cpod_p }}</b><br><span class="text-xs">P</span></div>
                        <div class="celda"><b>{{ $odontograma->cpod_o }}</b><br><span class="text-xs">O</span></div>
                        <div class="celda total"><b>{{ $odontograma->cpod_c + $odontograma->cpod_p + $odontograma->cpod_o }}</b><br><span class="text-xs">CPO-D</span></div>
                    </div>
                    <div class="flex">
                        <div class="celda"><b>{{ $odontograma->ceod_c }}</b><br><span class="text-xs">c</span></div>
                        <div class="celda"><b>{{ $odontograma->ceod_e }}</b><br><span class="text-xs">e</span></div>
                        <div class="celda"><b>{{ $odontograma->ceod_o }}</b><br><span class="text-xs">o</span></div>
                        <div class="celda total"><b>{{ $odontograma->ceod_c + $odontograma->ceod_e + $odontograma->ceod_o }}</b><br><span class="text-xs">ceo-d</span></div>
                    </div>
                </div>
            </div>

            @if ($odontograma->piezas->whereNotNull('movilidad')->isNotEmpty() || $odontograma->piezas->whereNotNull('recesion')->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-3">Movilidad / recesión (dientes definitivos)</p>
                    <div class="flex flex-wrap gap-3 text-sm">
                        @foreach ($odontograma->piezas->filter(fn ($p) => $p->movilidad !== null || $p->recesion !== null) as $pieza)
                            <span class="bg-gray-100 rounded px-2 py-1">
                                {{ $pieza->pieza }}: mov. {{ $pieza->movilidad ?? '—' }} / rec. {{ $pieza->recesion ?? '—' }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-3">Índice de higiene oral simplificada</p>

                @if ($odontograma->ihosRegistros->whereNotNull('pieza_examinada')->isNotEmpty())
                    <table class="text-sm mb-4 w-full">
                        <thead>
                            <tr class="text-left text-xs uppercase text-gray-500">
                                <th class="pb-1 pr-4">Sextante</th>
                                <th class="pb-1 pr-4">Pieza</th>
                                <th class="pb-1 pr-4">Placa</th>
                                <th class="pb-1 pr-4">Cálculo</th>
                                <th class="pb-1 pr-4">Gingivitis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($odontograma->ihosRegistros as $registro)
                                <tr class="border-t">
                                    <td class="py-1 pr-4">{{ $registro->sextante->numero }}</td>
                                    <td class="py-1 pr-4">{{ $registro->pieza_examinada ?? '— (no aplica)' }}</td>
                                    <td class="py-1 pr-4">{{ $registro->placa ?? '—' }}</td>
                                    <td class="py-1 pr-4">{{ $registro->calculo ?? '—' }}</td>
                                    <td class="py-1 pr-4">{{ $registro->gingivitis ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <div class="flex gap-6 flex-wrap text-sm mb-4">
                    <div class="bg-gray-50 rounded px-3 py-2">
                        <span class="text-gray-500 block text-xs">Promedio placa</span>
                        <b>{{ $odontograma->ihos_placa_promedio ?? 'Sin registrar' }}</b>
                    </div>
                    <div class="bg-gray-50 rounded px-3 py-2">
                        <span class="text-gray-500 block text-xs">Promedio cálculo</span>
                        <b>{{ $odontograma->ihos_calculo_promedio ?? 'Sin registrar' }}</b>
                    </div>
                    <div class="bg-gray-50 rounded px-3 py-2">
                        <span class="text-gray-500 block text-xs">Promedio gingivitis</span>
                        <b>{{ $odontograma->ihos_gingivitis_promedio ?? 'Sin registrar' }}</b>
                    </div>
                </div>

                <div class="flex gap-6 flex-wrap text-sm">
                    <div><span class="text-gray-500 block text-xs">Enfermedad periodontal</span><b>{{ ucfirst($odontograma->enfermedad_periodontal ?? 'sin registrar') }}</b></div>
                    <div><span class="text-gray-500 block text-xs">Tipo de oclusión</span><b>{{ $odontograma->tipo_oclusion ? 'Clase '.$odontograma->tipo_oclusion : 'Sin registrar' }}</b></div>
                    <div><span class="text-gray-500 block text-xs">Fluorosis</span><b>{{ ucfirst($odontograma->fluorosis ?? 'sin registrar') }}</b></div>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('historias.show', $odontograma->historiaClinica) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-md">
                    Volver a la historia clínica
                </a>
            </div>
        </div>
    </div>

    <script>
    function odontogramaVisor(condicionesIndexadas, hallazgosPorPieza) {
        const LADO = 34, BORDE = LADO * 0.30;
        return {
            CONDICIONES: condicionesIndexadas,
            datos: hallazgosPorPieza, // { pieza: { superficies: {cara: condId}, piezas: [condId,...] } }

            permanentes(q) {
                const n = [1,2,3,4,5,6,7,8].map(i => q * 10 + i);
                return [1,4].includes(q) ? n.reverse() : n;
            },
            temporales(q) {
                const n = [1,2,3,4,5].map(i => q * 10 + i);
                return [5,8].includes(q) ? n.reverse() : n;
            },
            tono(color) {
                return color === 'rojo' ? '#d3232a' : color === 'azul' ? '#1f52b8' : '#16191d';
            },
            anatomia(pieza) {
                const q = Math.floor(pieza / 10);
                const superior = [1, 2, 5, 6].includes(q);
                const derecho = [1, 4, 5, 8].includes(q);
                const posicion = pieza % 10;
                return {
                    arriba: superior ? 'vestibular' : 'lingual',
                    abajo: superior ? 'palatina' : 'vestibular',
                    izq: derecho ? 'distal' : 'mesial',
                    der: derecho ? 'mesial' : 'distal',
                    centro: posicion <= 3 ? 'incisal' : 'oclusal',
                };
            },
            relleno(pieza, superficie) {
                const info = this.datos[pieza];
                const condId = info?.superficies?.[superficie];
                if (!condId) return '#ffffff';
                return this.tono(this.CONDICIONES[condId].color);
            },
            dibujar(pieza) {
                const a = this.anatomia(pieza);
                const S = LADO, B = BORDE;
                const poligonos = {
                    arriba: `0,0 ${S},0 ${S-B},${B} ${B},${B}`,
                    der: `${S},0 ${S},${S} ${S-B},${S-B} ${S-B},${B}`,
                    abajo: `${S},${S} 0,${S} ${B},${S-B} ${S-B},${S-B}`,
                    izq: `0,${S} 0,0 ${B},${B} ${B},${S-B}`,
                    centro: `${B},${B} ${S-B},${B} ${S-B},${S-B} ${B},${S-B}`,
                };
                const caras = Object.entries(poligonos).map(([zona, puntos]) => {
                    const sup = a[zona];
                    return `<polygon points="${puntos}" fill="${this.relleno(pieza, sup)}" stroke="#9aa3ae" stroke-width="1"></polygon>`;
                }).join('');
                const marcasIds = this.datos[pieza]?.piezas || [];
                const marcas = marcasIds.map((id, i) => {
                    const c = this.CONDICIONES[id];
                    if (!c) return '';
                    return `<text x="${S/2}" y="${S/2 + 6 + (i % 2 ? 11 : 0)}" text-anchor="middle"
                              font-size="${i % 2 ? 13 : 20}" fill="${this.tono(c.color)}"
                              style="font-weight:700">${c.simbolo || ''}</text>`;
                }).join('');
                return `<div class="odonto-pieza">
                          <svg width="${S}" height="${S}" viewBox="0 0 ${S} ${S}">${caras}${marcas}</svg>
                          <span class="numero">${pieza}</span>
                        </div>`;
            },
        };
    }
    </script>
</x-app-layout>
