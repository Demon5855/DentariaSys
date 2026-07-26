<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Odontograma ({{ $tipoSugerido === 'inicial' ? 'Inicial' : 'Evolutivo' }}):
            <span class="text-indigo-600">{{ $historiaClinica->paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <style>
        :root {
            --tinta: #16191d; --tinta-suave: #5b6470; --linea: #d3d8de; --linea-fuerte: #9aa3ae;
            --papel: #ffffff; --patologia: #d3232a; --tratamiento: #1f52b8; --foco: #0f9d8f;
        }
        .odonto-lienzo * { box-sizing: border-box; }
        .odonto-lienzo { font-size: 14px; line-height: 1.5; color: var(--tinta); }
        .odonto-rejilla { display: grid; grid-template-columns: minmax(0,1fr) 280px; gap: 22px; align-items: start; }
        @media (max-width: 1024px) { .odonto-rejilla { grid-template-columns: 1fr; } }
        .odonto-tarjeta { background: var(--papel); border: 1px solid var(--linea); border-radius: 6px; padding: 18px; }
        .odonto-rotulo { font-size: 10.5px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: var(--tinta-suave); margin: 0 0 10px; }
        .odonto-arcada { display: flex; justify-content: center; gap: 26px; }
        .odonto-hemi { display: flex; gap: 3px; }
        .odonto-fila + .odonto-fila { margin-top: 14px; }
        .odonto-eje { display: flex; align-items: center; gap: 10px; margin: 16px 0; color: var(--linea-fuerte); font-size: 10px; letter-spacing: .12em; text-transform: uppercase; }
        .odonto-eje::before, .odonto-eje::after { content: ""; flex: 1; height: 1px; background: var(--linea); }
        .odonto-pieza { display: flex; flex-direction: column; align-items: center; gap: 2px; }
        .odonto-pieza .numero { font-size: 10.5px; color: var(--tinta-suave); }
        .odonto-cara { cursor: pointer; transition: fill .08s; }
        .odonto-cara:hover { fill: #e8edf3 !important; }
        .odonto-indices-per { display: flex; justify-content: center; gap: 26px; margin-top: 6px; }
        .odonto-fila-mini { display: flex; gap: 3px; }
        .odonto-fila-mini input { width: 38px; height: 20px; padding: 0; text-align: center; font-size: 10.5px; border: 1px solid var(--linea); border-radius: 2px; background: #fafbfc; }
        .odonto-etiq-mini { font-size: 9.5px; letter-spacing: .08em; text-transform: uppercase; color: var(--tinta-suave); text-align: center; margin: 8px 0 3px; }
        .odonto-opcion { display: flex; align-items: center; gap: 9px; width: 100%; padding: 6px 8px; margin-bottom: 2px; font-size: 12.5px; text-align: left; background: transparent; border: 1px solid transparent; border-radius: 4px; cursor: pointer; }
        .odonto-opcion:hover { background: #f0f3f6; }
        .odonto-opcion[aria-pressed="true"] { background: #eef2f7; border-color: var(--linea-fuerte); font-weight: 600; }
        .odonto-muestra { width: 15px; height: 15px; flex: none; border-radius: 2px; border: 1px solid rgba(0,0,0,.18); display: grid; place-items: center; font-size: 10px; font-weight: 700; }
        .odonto-indices .celda { min-width: 42px; padding: 5px 8px; text-align: center; background: #f6f8fa; border: 1px solid var(--linea); }
        .odonto-indices .celda.total { background: var(--tinta); color: #fff; border-color: var(--tinta); }
    </style>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <p class="font-bold mb-1">Revisa lo siguiente:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('odontogramas.store', $historiaClinica) }}" method="POST"
                @submit="prepararEnvio($el)"
                x-data="odontogramaForm(@js($condiciones->keyBy('id')), @js($sextantesIhos), '{{ $tipoSugerido }}')">
                @csrf

                <div class="odonto-lienzo" x-cloak>

                    <div class="odonto-tarjeta mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Tipo" />
                                <select name="tipo" x-model="tipo" required
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="inicial">Inicial</option>
                                    <option value="evolutivo">Evolutivo</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Dentición" />
                                <select name="denticion" x-model="denticion" required
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="permanente">Permanente</option>
                                    <option value="mixta">Mixta</option>
                                    <option value="temporal">Temporal</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Fecha" />
                                <x-text-input type="date" name="fecha" class="block mt-1 w-full"
                                    :value="old('fecha', now()->toDateString())" required />
                            </div>
                        </div>
                    </div>

                    <div class="odonto-rejilla">
                        <section class="odonto-tarjeta">

                            <template x-if="denticion !== 'temporal'">
                                <div>
                                    <p class="odonto-etiq-mini">Movilidad · Recesión</p>
                                    <div class="odonto-indices-per">
                                        <template x-for="lado in [permanentes(1), permanentes(2)]" :key="lado[0]">
                                            <div class="odonto-fila-mini">
                                                <template x-for="p in lado" :key="p">
                                                    <input type="text" inputmode="numeric" maxlength="3"
                                                        :aria-label="'Movilidad y recesión pieza ' + p"
                                                        x-model="periodontal[p]">
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <div class="odonto-fila" x-show="denticion !== 'temporal'">
                                <div class="odonto-arcada">
                                    <div class="odonto-hemi"><template x-for="p in permanentes(1)" :key="p"><div x-html="dibujar(p)" @click="pintar($event)"></div></template></div>
                                    <div class="odonto-hemi"><template x-for="p in permanentes(2)" :key="p"><div x-html="dibujar(p)" @click="pintar($event)"></div></template></div>
                                </div>
                            </div>

                            <div class="odonto-fila" x-show="denticion !== 'permanente'">
                                <div class="odonto-arcada">
                                    <div class="odonto-hemi"><template x-for="p in temporales(5)" :key="p"><div x-html="dibujar(p)" @click="pintar($event)"></div></template></div>
                                    <div class="odonto-hemi"><template x-for="p in temporales(6)" :key="p"><div x-html="dibujar(p)" @click="pintar($event)"></div></template></div>
                                </div>
                            </div>

                            <p class="odonto-eje">Línea media</p>

                            <div class="odonto-fila" x-show="denticion !== 'permanente'">
                                <div class="odonto-arcada">
                                    <div class="odonto-hemi"><template x-for="p in temporales(8)" :key="p"><div x-html="dibujar(p)" @click="pintar($event)"></div></template></div>
                                    <div class="odonto-hemi"><template x-for="p in temporales(7)" :key="p"><div x-html="dibujar(p)" @click="pintar($event)"></div></template></div>
                                </div>
                            </div>

                            <div class="odonto-fila" x-show="denticion !== 'temporal'">
                                <div class="odonto-arcada">
                                    <div class="odonto-hemi"><template x-for="p in permanentes(4)" :key="p"><div x-html="dibujar(p)" @click="pintar($event)"></div></template></div>
                                    <div class="odonto-hemi"><template x-for="p in permanentes(3)" :key="p"><div x-html="dibujar(p)" @click="pintar($event)"></div></template></div>
                                </div>
                            </div>

                            <template x-if="denticion !== 'temporal'">
                                <div class="odonto-indices-per" style="margin-top:8px">
                                    <template x-for="lado in [permanentes(4), permanentes(3)]" :key="lado[0]">
                                        <div class="odonto-fila-mini">
                                            <template x-for="p in lado" :key="p">
                                                <input type="text" inputmode="numeric" maxlength="3"
                                                    :aria-label="'Movilidad y recesión pieza ' + p"
                                                    x-model="periodontal[p]">
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </section>

                        <aside class="odonto-tarjeta">
                            <p class="odonto-rotulo">Herramienta</p>

                            <div class="mb-4">
                                <p style="margin:0 0 5px;font-size:11px;color:var(--tinta-suave)">Por superficie</p>
                                <template x-for="c in Object.values(CONDICIONES).filter(c => c.nivel === 'superficie')" :key="c.id">
                                    <button type="button" class="odonto-opcion" :aria-pressed="herramienta === c.id" @click="herramienta = c.id">
                                        <span class="odonto-muestra" :style="`background:${tono(c.color)}`"></span>
                                        <span x-text="c.nombre"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="mb-4">
                                <p style="margin:0 0 5px;font-size:11px;color:var(--tinta-suave)">Por pieza completa</p>
                                <template x-for="c in Object.values(CONDICIONES).filter(c => c.nivel === 'pieza')" :key="c.id">
                                    <button type="button" class="odonto-opcion" :aria-pressed="herramienta === c.id" @click="herramienta = c.id">
                                        <span class="odonto-muestra" :style="`color:${tono(c.color)};background:#fff`" x-text="c.simbolo"></span>
                                        <span x-text="c.nombre"></span>
                                    </button>
                                </template>
                            </div>

                            <button type="button" class="odonto-opcion" :aria-pressed="herramienta === 'borrar'" @click="herramienta = 'borrar'">
                                <span class="odonto-muestra" style="background:#fff">⌫</span>
                                <span>Borrar hallazgo</span>
                            </button>

                            <div class="mt-4 pt-4 border-t">
                                <p class="text-xs text-gray-500 mb-3">
                                    Al firmar, este odontograma queda bloqueado. Las correcciones se hacen
                                    registrando uno nuevo, de tipo evolutivo.
                                </p>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-sm">
                                    Firmar odontograma
                                </button>
                                <a href="{{ route('historias.show', $historiaClinica) }}" class="block text-center text-sm text-gray-600 hover:text-gray-900 mt-3">Cancelar</a>
                            </div>
                        </aside>
                    </div>

                    <section class="odonto-tarjeta mt-4">
                        <p class="odonto-rotulo">Índices CPO-D / ceo-d (referencial — el servidor recalcula al guardar)</p>
                        <div class="flex gap-8 flex-wrap odonto-indices">
                            <div class="flex">
                                <div class="celda"><b x-text="cpod.c"></b><br><span class="text-xs">C</span></div>
                                <div class="celda"><b x-text="cpod.p"></b><br><span class="text-xs">P</span></div>
                                <div class="celda"><b x-text="cpod.o"></b><br><span class="text-xs">O</span></div>
                                <div class="celda total"><b x-text="cpod.c + cpod.p + cpod.o"></b><br><span class="text-xs">CPO-D</span></div>
                            </div>
                            <div class="flex">
                                <div class="celda"><b x-text="ceod.c"></b><br><span class="text-xs">c</span></div>
                                <div class="celda"><b x-text="ceod.e"></b><br><span class="text-xs">e</span></div>
                                <div class="celda"><b x-text="ceod.o"></b><br><span class="text-xs">o</span></div>
                                <div class="celda total"><b x-text="ceod.c + ceod.e + ceod.o"></b><br><span class="text-xs">ceo-d</span></div>
                            </div>
                        </div>
                    </section>

                    <section class="odonto-tarjeta mt-4">
                        <p class="odonto-rotulo">Índice de higiene oral simplificada (sección I)</p>
                        <p class="text-xs text-gray-500 mb-3">
                            Por cada sextante, indica qué pieza examinaste (si la primaria no está en boca,
                            usa la alterna; si ninguna está presente, deja "No aplica" — no cuenta en el promedio).
                        </p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-gray-500">
                                        <th class="pb-2 pr-4">Sextante</th>
                                        <th class="pb-2 pr-4">Pieza examinada</th>
                                        <th class="pb-2 pr-4">Placa (0-3)</th>
                                        <th class="pb-2 pr-4">Cálculo (0-3)</th>
                                        <th class="pb-2 pr-4">Gingivitis (0-1)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sextantesIhos as $sextante)
                                        <tr class="border-t">
                                            <td class="py-2 pr-4 font-medium">{{ $sextante->numero }}</td>
                                            <td class="py-2 pr-4">
                                                <select x-model="ihos[{{ $sextante->id }}].pieza"
                                                    class="border-gray-300 rounded-md text-sm">
                                                    <option value="">No aplica (—)</option>
                                                    <option value="{{ $sextante->pieza_primaria }}">{{ $sextante->pieza_primaria }} (primaria)</option>
                                                    <option value="{{ $sextante->pieza_alterna }}">{{ $sextante->pieza_alterna }} (alterna)</option>
                                                    @if ($sextante->pieza_temporal)
                                                        <option value="{{ $sextante->pieza_temporal }}">{{ $sextante->pieza_temporal }} (temporal)</option>
                                                    @endif
                                                </select>
                                            </td>
                                            <td class="py-2 pr-4">
                                                <input type="number" min="0" max="3" x-model="ihos[{{ $sextante->id }}].placa"
                                                    class="w-16 border-gray-300 rounded-md text-sm" :disabled="!ihos[{{ $sextante->id }}].pieza">
                                            </td>
                                            <td class="py-2 pr-4">
                                                <input type="number" min="0" max="3" x-model="ihos[{{ $sextante->id }}].calculo"
                                                    class="w-16 border-gray-300 rounded-md text-sm" :disabled="!ihos[{{ $sextante->id }}].pieza">
                                            </td>
                                            <td class="py-2 pr-4">
                                                <input type="number" min="0" max="1" x-model="ihos[{{ $sextante->id }}].gingivitis"
                                                    class="w-16 border-gray-300 rounded-md text-sm" :disabled="!ihos[{{ $sextante->id }}].pieza">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="odonto-tarjeta mt-4">
                        <p class="odonto-rotulo">Resto de la sección I</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Enfermedad periodontal" />
                                <select name="enfermedad_periodontal" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="ninguna">Ninguna</option>
                                    <option value="leve">Leve</option>
                                    <option value="moderada">Moderada</option>
                                    <option value="avanzada">Avanzada</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Tipo de oclusión (Angle)" />
                                <select name="tipo_oclusion" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Sin registrar</option>
                                    <option value="I">Clase I (neutroclusión)</option>
                                    <option value="II">Clase II (distoclusión)</option>
                                    <option value="III">Clase III (mesioclusión)</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Fluorosis (Dean)" />
                                <select name="fluorosis" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="ninguna">Ninguna</option>
                                    <option value="leve">Leve</option>
                                    <option value="moderada">Moderada</option>
                                    <option value="severa">Severa</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- Aquí se insertan los inputs hallazgos[]/periodontal[]/ihos[] justo antes de enviar --}}
                    <div id="odonto-campos-dinamicos"></div>
                </div>
            </form>
        </div>
    </div>

    <script>
    function odontogramaForm(condicionesIniciales, sextantesIhos, tipoSugerido) {
        const LADO = 34, BORDE = LADO * 0.30;

        const ihosInicial = {};
        sextantesIhos.forEach(s => {
            ihosInicial[s.id] = { pieza: '', placa: '', calculo: '', gingivitis: '' };
        });

        return {
            CONDICIONES: condicionesIniciales,
            ihos: ihosInicial,
            tipo: tipoSugerido,
            denticion: 'permanente',
            herramienta: Object.keys(condicionesIniciales)[0] ? Number(Object.keys(condicionesIniciales)[0]) : 'borrar',
            superficies: {},  // { pieza: { superficie: condicionId } }
            piezas: {},       // { pieza: [condicionId, ...] }
            periodontal: {},  // { pieza: 'movilidad/recesion' }

            permanentes(q) {
                const n = [1,2,3,4,5,6,7,8].map(i => q * 10 + i);
                return [1,4].includes(q) ? n.reverse() : n;
            },
            temporales(q) {
                const n = [1,2,3,4,5].map(i => q * 10 + i);
                return [5,8].includes(q) ? n.reverse() : n;
            },
            tono(color) {
                return color === 'rojo' ? 'var(--patologia)' : color === 'azul' ? 'var(--tratamiento)' : 'var(--tinta)';
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
                const condId = this.superficies[pieza]?.[superficie];
                if (!condId) return '#ffffff';
                return this.tono(this.CONDICIONES[condId].color);
            },
            ausente(pieza) {
                return (this.piezas[pieza] || []).some(id => {
                    const c = this.CONDICIONES[id];
                    return c && (c.clave === 'perdida_caries' || c.clave === 'perdida_otra');
                });
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
                    return `<polygon class="odonto-cara" points="${puntos}" fill="${this.relleno(pieza, sup)}"
                              stroke="var(--linea-fuerte)" stroke-width="1"
                              data-pieza="${pieza}" data-superficie="${sup}"></polygon>`;
                }).join('');
                const marcas = (this.piezas[pieza] || []).map((id, i) => {
                    const c = this.CONDICIONES[id];
                    if (!c) return '';
                    return `<text x="${S/2}" y="${S/2 + 6 + (i % 2 ? 11 : 0)}" text-anchor="middle"
                              font-size="${i % 2 ? 13 : 20}" fill="${this.tono(c.color)}"
                              style="pointer-events:none;font-weight:700">${c.simbolo || ''}</text>`;
                }).join('');
                return `<div class="odonto-pieza ${this.ausente(pieza) ? 'text-gray-300' : ''}">
                          <svg width="${S}" height="${S}" viewBox="0 0 ${S} ${S}">${caras}${marcas}</svg>
                          <span class="numero">${pieza}</span>
                        </div>`;
            },
            pintar(evento) {
                const cara = evento.target.closest('.odonto-cara');
                if (!cara) return;
                const pieza = Number(cara.dataset.pieza);
                const superficie = cara.dataset.superficie;
                this.aplicar(pieza, superficie);
            },
            aplicar(pieza, superficie) {
                if (this.herramienta === 'borrar') {
                    if (this.superficies[pieza]) delete this.superficies[pieza][superficie];
                    delete this.piezas[pieza];
                    this.superficies = { ...this.superficies };
                    this.piezas = { ...this.piezas };
                    return;
                }
                const cond = this.CONDICIONES[this.herramienta];
                if (!cond) return;

                if (cond.nivel === 'superficie') {
                    this.superficies[pieza] = { ...(this.superficies[pieza] || {}) };
                    if (this.superficies[pieza][superficie] === this.herramienta) {
                        delete this.superficies[pieza][superficie];
                    } else {
                        this.superficies[pieza][superficie] = this.herramienta;
                    }
                    this.superficies = { ...this.superficies };
                } else {
                    const actuales = this.piezas[pieza] || [];
                    this.piezas[pieza] = actuales.includes(this.herramienta)
                        ? actuales.filter(id => id !== this.herramienta)
                        : [...actuales, this.herramienta];
                    this.piezas = { ...this.piezas };
                }
            },
            clasificar(pieza) {
                const dePieza = (this.piezas[pieza] || []).map(id => this.CONDICIONES[id]).filter(Boolean);
                const deSup = Object.values(this.superficies[pieza] || {}).map(id => this.CONDICIONES[id]).filter(Boolean);
                const todas = [...dePieza, ...deSup];

                const excluida = todas.some(c => c.excluye_terceros_molares && [18,28,38,48].includes(Number(pieza)));
                if (excluida) return null;

                const indices = todas.map(c => c.afecta_indice).filter(Boolean);
                if (indices.includes('P')) return 'P';
                if (indices.includes('C')) return 'C';
                if (indices.includes('O')) return 'O';
                return null;
            },
            contar(rango) {
                const r = { c: 0, p: 0, o: 0 };
                const universo = new Set([
                    ...Object.keys(this.superficies), ...Object.keys(this.piezas),
                ].map(Number).filter(p => rango.includes(Math.floor(p / 10))));
                universo.forEach(p => {
                    const clase = this.clasificar(p);
                    if (clase === 'C') r.c++; else if (clase === 'P') r.p++; else if (clase === 'O') r.o++;
                });
                return r;
            },
            get cpod() { return this.contar([1,2,3,4]); },
            get ceod() {
                const r = this.contar([5,6,7,8]);
                return { c: r.c, e: r.p, o: r.o };
            },

            // Construye hallazgos[]/periodontal[] como inputs reales antes
            // de que el navegador serialice el <form> — así el backend
            // recibe arrays anidados estándar de Laravel, sin fetch ni JSON.
            prepararEnvio(formEl) {
                const contenedor = formEl.querySelector('#odonto-campos-dinamicos');
                contenedor.innerHTML = '';
                let indice = 0;

                const agregarInput = (nombre, valor) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = nombre;
                    input.value = valor;
                    contenedor.appendChild(input);
                };

                Object.entries(this.superficies).forEach(([pieza, caras]) => {
                    Object.entries(caras).forEach(([superficie, condId]) => {
                        agregarInput(`hallazgos[${indice}][pieza]`, pieza);
                        agregarInput(`hallazgos[${indice}][condicion_id]`, condId);
                        agregarInput(`hallazgos[${indice}][superficie]`, superficie);
                        indice++;
                    });
                });

                Object.entries(this.piezas).forEach(([pieza, condiciones]) => {
                    condiciones.forEach(condId => {
                        agregarInput(`hallazgos[${indice}][pieza]`, pieza);
                        agregarInput(`hallazgos[${indice}][condicion_id]`, condId);
                        indice++;
                    });
                });

                let indicePeriodontal = 0;
                Object.entries(this.periodontal).forEach(([pieza, valor]) => {
                    if (!valor || !valor.trim()) return;
                    const [movilidad, recesion] = valor.split('/');
                    agregarInput(`periodontal[${indicePeriodontal}][pieza]`, pieza);
                    if (movilidad) agregarInput(`periodontal[${indicePeriodontal}][movilidad]`, movilidad);
                    if (recesion) agregarInput(`periodontal[${indicePeriodontal}][recesion]`, recesion);
                    indicePeriodontal++;
                });

                let indiceIhos = 0;
                Object.entries(this.ihos).forEach(([sextanteId, valores]) => {
                    if (!valores.pieza) return; // sextante marcado "No aplica"
                    agregarInput(`ihos[${indiceIhos}][sextante_id]`, sextanteId);
                    agregarInput(`ihos[${indiceIhos}][pieza_examinada]`, valores.pieza);
                    if (valores.placa !== '') agregarInput(`ihos[${indiceIhos}][placa]`, valores.placa);
                    if (valores.calculo !== '') agregarInput(`ihos[${indiceIhos}][calculo]`, valores.calculo);
                    if (valores.gingivitis !== '') agregarInput(`ihos[${indiceIhos}][gingivitis]`, valores.gingivitis);
                    indiceIhos++;
                });
            },
        };
    }
    </script>
</x-app-layout>
