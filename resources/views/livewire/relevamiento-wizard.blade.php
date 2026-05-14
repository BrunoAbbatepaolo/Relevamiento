<?php

use App\Models\Area;
use App\Models\Oficina;
use App\Models\Relevamiento;
use App\Models\Servidor;
use App\Models\Activo;
use App\Models\EquipoAdicional;
use Illuminate\Support\Str;
use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    // ─── Wizard state ──────────────────────────────────────────────
    public int $step = 1;
    public int $totalSteps = 4;

    // ─── Persisted relevamiento id ────────────────────────────────
    public ?int $relevamientoId = null;

    // ══════════════════════════════════════════════════════════════
    // STEP 1 — Datos Generales
    // ══════════════════════════════════════════════════════════════
    public ?int $area_id = null;
    public ?int $oficina_id = null;
    public string $responsable_nombre = '';

    // ══════════════════════════════════════════════════════════════
    // STEP 2 — Servidor e Impresoras
    // ══════════════════════════════════════════════════════════════
    public bool $tiene_servidor = false;
    public string $srv_funcion = '';
    public string $srv_so = '';
    public string $srv_so_version = '';
    public string $srv_requerimientos = '';
    public string $srv_tipo_hw = '';
    public string $srv_cpu_ghz = '';
    public string $srv_ram_tipo = '';
    public string $srv_ram_gb = '';
    public array $srv_discos = [];
    public string $srv_swap_gb = '';
    public string $srv_motor_bd = '';
    public string $srv_cantidad_bases = '';
    public string $srv_tamano_bases = '';
    public string $srv_usuarios_concurrentes = '';
    public string $srv_usuarios_totales = '';
    public string $srv_crecimiento = '';
    public string $srv_licencia_tipo = '';
    public string $srv_licencia_cantidad = '';
    public string $srv_licencia_obs = '';
    public string $srv_red_lan = '';
    public string $srv_wan = '';
    public string $srv_internet = '';
    public string $srv_trafico = '';

    public bool $tiene_impresora = false;
    public array $impresoras = [];

    public bool $tiene_modem = false;
    public array $modems = [];

    // ══════════════════════════════════════════════════════════════
    // STEP 3 — Activos (PCs / Notebooks)
    // ══════════════════════════════════════════════════════════════
    public array $activos = [];
    public int $activoEditando = -1; // índice del activo en edición, -1 = ninguno

    // Formulario del activo actual
    public array $activoForm = [];

    // ══════════════════════════════════════════════════════════════
    // STEP 4 — Equipos Adicionales
    // ══════════════════════════════════════════════════════════════
    public bool $eq_tiene_proyector = false;
    public string $eq_proyector_marca = '';
    public string $eq_proyector_conexion = '';
    public string $eq_proyector_id = '';

    public bool $eq_tiene_led = false;
    public string $eq_led_marca = '';
    public string $eq_led_pulgadas = '';
    public string $eq_led_conexion = '';
    public string $eq_led_id = '';

    public bool $eq_tiene_camara = false;
    public string $eq_camara_marca = '';
    public string $eq_camara_conexion = '';
    public string $eq_camara_id = '';

    public int $eq_cantidad_vigilancia = 0;
    public string $eq_vigilancia_ids = '';

    public array $eq_otros = [];
    public string $eq_otro_desc = '';
    public string $eq_otro_id = '';

    // ─── Computed ──────────────────────────────────────────────────
    public function getAreasProperty(): Collection
    {
        return Area::orderBy('nombre')->get();
    }

    public function getOficinasProperty(): Collection
    {
        if (!$this->area_id) {
            return collect();
        }
        return Oficina::where('cod_area', $this->area_id)->orderBy('nombre')->get();
    }

    // ─── Lifecycle ─────────────────────────────────────────────────
    public function mount(?int $id = null): void
    {
        $this->resetActivoForm();

        if ($id) {
            $this->cargarRelevamiento($id);
            // Deep link a un activo específico
            if ($activoId = request()->query('activo')) {
                $this->step = 3;
                foreach ($this->activos as $index => $a) {
                    if (isset($a['id']) && $a['id'] == $activoId) {
                        $this->editarActivo($index);
                        $this->modalActivo = true;
                        break;
                    }
                }
            }
        }
    }

    public function updatedOficinaId($value)
    {
        if ($value) {
            $existente = Relevamiento::where('oficina_id', $value)->first();
            if ($existente) {
                $this->cargarRelevamiento($existente->id);
            } else {
                $this->relevamientoId = null;
                $this->responsable_nombre = '';
                $this->tiene_servidor = false;
                $this->tiene_impresora = false;
                $this->activos = [];
            }
        }
    }

    private function cargarRelevamiento(int $id): void
    {
        $r = Relevamiento::with(['servidor', 'activos', 'equiposAdicionales'])->findOrFail($id);
        $this->relevamientoId = $r->id;
        $this->area_id = $r->area_id;
        $this->oficina_id = $r->oficina_id;
        $this->responsable_nombre = $r->responsable_nombre;

        $this->tiene_impresora = $r->tiene_impresora ?? false;
        $this->impresoras = $r->impresoras ?? [];

        $this->tiene_modem = $r->tiene_modem ?? false;
        $this->modems = $r->modems ?? [];

        if ($srv = $r->servidor) {
            $this->tiene_servidor = $srv->tiene_servidor;
            $this->srv_funcion = $srv->funcion ?? '';
            $this->srv_so = $srv->sistema_operativo ?? '';
            $this->srv_so_version = $srv->so_version ?? '';
            $this->srv_requerimientos = $srv->requerimientos_especiales ?? '';
            $this->srv_tipo_hw = $srv->tipo_equipamiento ?? '';
            $this->srv_cpu_ghz = (string) ($srv->cpu_ghz ?? '');
            $this->srv_ram_tipo = $srv->ram_tipo ?? '';
            $this->srv_ram_gb = (string) ($srv->ram_gb ?? '');
            $this->srv_discos = $srv->almacenamiento_discos ?? [];
            $this->srv_swap_gb = (string) ($srv->swap_gb ?? '');
            $this->srv_motor_bd = $srv->motor_bd ?? '';
            $this->srv_cantidad_bases = (string) ($srv->cantidad_bases ?? '');
            $this->srv_tamano_bases = (string) ($srv->tamano_bases_gb ?? '');
            $this->srv_usuarios_concurrentes = (string) ($srv->usuarios_concurrentes ?? '');
            $this->srv_usuarios_totales = (string) ($srv->usuarios_totales ?? '');
            $this->srv_crecimiento = $srv->estimacion_crecimiento ?? '';
            $this->srv_licencia_tipo = $srv->licencia_tipo ?? '';
            $this->srv_licencia_cantidad = (string) ($srv->licencia_cantidad ?? '');
            $this->srv_licencia_obs = $srv->licencia_observaciones ?? '';
            $this->srv_red_lan = $srv->red_lan ?? '';
            $this->srv_wan = $srv->wan ?? '';
            $this->srv_internet = $srv->internet_dedicado ?? '';
            $this->srv_trafico = (string) ($srv->trafico_estimado_mb ?? '');
        }

        $this->activos = $r->activos->map(fn($a) => $a->toArray())->toArray();

        if ($eq = $r->equiposAdicionales) {
            $this->eq_tiene_proyector = $eq->tiene_proyector;
            $this->eq_proyector_marca = $eq->proyector_marca ?? '';
            $this->eq_proyector_conexion = $eq->proyector_conexion ?? '';
            $this->eq_proyector_id = $eq->proyector_id_inventario ?? '';
            $this->eq_tiene_led = $eq->tiene_led;
            $this->eq_led_marca = $eq->led_marca ?? '';
            $this->eq_led_pulgadas = (string) ($eq->led_pulgadas ?? '');
            $this->eq_led_conexion = $eq->led_conexion ?? '';
            $this->eq_led_id = $eq->led_id_inventario ?? '';
            $this->eq_tiene_camara = $eq->tiene_camara;
            $this->eq_camara_marca = $eq->camara_marca ?? '';
            $this->eq_camara_conexion = $eq->camara_conexion ?? '';
            $this->eq_camara_id = $eq->camara_id_inventario ?? '';
            $this->eq_cantidad_vigilancia = $eq->cantidad_camaras_vigilancia;
            $this->eq_vigilancia_ids = implode(', ', $eq->camaras_vigilancia_ids ?? []);
            $this->eq_otros = $eq->otros_dispositivos ?? [];
        }
    }

    // ─── Navigation ────────────────────────────────────────────────
    public function siguientePaso(): void
    {
        $this->guardarPasoActual();
        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function pasoAnterior(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function irAPaso(int $paso): void
    {
        if ($paso <= $this->step || $this->relevamientoId) {
            $this->guardarPasoActual();
            $this->step = $paso;
        }
    }

    // ─── Save per step ─────────────────────────────────────────────
    private function guardarPasoActual(): void
    {
        match ($this->step) {
            1 => $this->guardarDatosGenerales(),
            2 => $this->guardarServidor(),
            3 => null, // activos se guardan individualmente
            4 => $this->guardarEquiposAdicionales(),
            default => null,
        };
    }

    public function guardarDatosGenerales(): void
    {
        $validated = $this->validate([
            'area_id' => 'required|exists:external_db.areas,id',
            'oficina_id' => 'required|exists:external_db.oficinas,id',
            'responsable_nombre' => 'required|string|max:255',
        ]);

        $data = [
            'area_id' => $this->area_id,
            'oficina_id' => $this->oficina_id,
            'responsable_nombre' => $this->responsable_nombre,
            'user_id' => auth()->id(),
        ];

        if ($this->relevamientoId) {
            Relevamiento::find($this->relevamientoId)?->update($data);
        } else {
            $r = Relevamiento::create($data);
            $this->relevamientoId = $r->id;
        }
    }

    // ─── Impresoras ───────────────────────────────────────────────
    public function agregarImpresora(): void
    {
        $this->impresoras[] = [
            'marca' => '',
            'modelo' => '',
            'ip' => '',
            'conexion' => 'red', // red o lan
            'escaner' => false,
            'tipo' => 'toner',
        ];
    }

    public function quitarImpresora(int $index): void
    {
        unset($this->impresoras[$index]);
        $this->impresoras = array_values($this->impresoras);
    }

    // ─── Modems ───────────────────────────────────────────────────
    public function agregarModem(): void
    {
        $this->modems[] = [
            'marca' => '',
            'modelo' => '',
            'ssid' => '',
            'password' => '',
            'ip' => '',
            'proxy' => '',
            'usuario' => '',
            'clave' => '',
            'mac' => '',
        ];
    }

    public function updatedModems($value, $key): void
    {
        if (str_ends_with($key, '.mac')) {
            $parts = explode('.', $key);
            $index = (int) $parts[1];
            $clean = preg_replace('/[^a-fA-F0-9]/', '', $value);
            $clean = substr($clean, 0, 12);
            $formatted = implode(':', str_split($clean, 2));
            $this->modems[$index]['mac'] = strtoupper($formatted);
        }
    }

    public function quitarModem(int $index): void
    {
        unset($this->modems[$index]);
        $this->modems = array_values($this->modems);
    }

    public function guardarServidor(): void
    {
        if (!$this->relevamientoId) {
            return;
        }

        $data = [
            'relevamiento_id' => $this->relevamientoId,
            'tiene_servidor' => $this->tiene_servidor,
            'funcion' => $this->srv_funcion ?: null,
            'sistema_operativo' => $this->srv_so ?: null,
            'so_version' => $this->srv_so_version ?: null,
            'requerimientos_especiales' => $this->srv_requerimientos ?: null,
            'tipo_equipamiento' => $this->srv_tipo_hw ?: null,
            'cpu_ghz' => $this->srv_cpu_ghz ?: null,
            'ram_tipo' => $this->srv_ram_tipo ?: null,
            'ram_gb' => $this->srv_ram_gb ?: null,
            'almacenamiento_discos' => $this->srv_discos,
            'swap_gb' => $this->srv_swap_gb ?: null,
            'motor_bd' => $this->srv_motor_bd ?: null,
            'cantidad_bases' => $this->srv_cantidad_bases ?: null,
            'tamano_bases_gb' => $this->srv_tamano_bases ?: null,
            'usuarios_concurrentes' => $this->srv_usuarios_concurrentes ?: null,
            'usuarios_totales' => $this->srv_usuarios_totales ?: null,
            'estimacion_crecimiento' => $this->srv_crecimiento ?: null,
            'licencia_tipo' => $this->srv_licencia_tipo ?: null,
            'licencia_cantidad' => $this->srv_licencia_cantidad ?: null,
            'licencia_observaciones' => $this->srv_licencia_obs ?: null,
            'red_lan' => $this->srv_red_lan ?: null,
            'wan' => $this->srv_wan ?: null,
            'internet_dedicado' => $this->srv_internet ?: null,
            'trafico_estimado_mb' => $this->srv_trafico ?: null,
        ];

        Servidor::updateOrCreate(['relevamiento_id' => $this->relevamientoId], $data);

        Relevamiento::find($this->relevamientoId)?->update([
            'tiene_impresora' => $this->tiene_impresora,
            'impresoras' => $this->impresoras,
            'tiene_modem' => $this->tiene_modem,
            'modems' => $this->modems,
        ]);
    }

    // ─── Activos CRUD ──────────────────────────────────────────────
    private function resetActivoForm(): void
    {
        $this->activoForm = [
            'codigo_inventario' => '',
            'tipo' => 'desktop',
            'estado' => 'activo',
            'tipo_red' => 'ip_fija',
            'ip' => '',
            'mac' => '',
            'so_tipo' => '',
            'so_version' => '',
            'marca' => '',
            'cpu_ghz' => '',
            'cpu_modelo' => '',
            'ram_tipo' => '',
            'ram_gb' => '',
            'discos' => [],
            'placa_video' => '',
            'monitores' => [],
            'tiene_mouse' => false,
            'mouse_marca' => '',
            'mouse_modelo' => '',
            'mouse_conexion' => 'usb',
            'tiene_teclado' => false,
            'teclado_marca' => '',
            'teclado_modelo' => '',
            'teclado_conexion' => 'usb',
            'estabilizador_marca' => '',
            'estabilizador_modelo' => '',
            'estabilizador_color' => '',
            'tiene_estabilizador' => false,
            'tiene_camara' => false,
            'camara_marca' => '',
            'camara_modelo' => '',
            'camara_conexion' => 'usb',
            'tiene_parlantes' => false,
            'parlantes_marca' => '',
            'parlantes_modelo' => '',
            'software_checkboxes' => [],
            'software_otros' => '',
            'software_instalado' => [],
            'usuario_nombre' => '',
            'usuario_apellido' => '',
            'usuario_caracter' => '',
        ];
    }

    public function updatedActivoFormMac($value)
    {
        $mac = preg_replace('/[^A-Fa-f0-9]/', '', $value);
        $mac = implode(':', str_split(strtoupper($mac), 2));
        $this->activoForm['mac'] = substr($mac, 0, 17);
    }

    public function nuevoActivo(): void
    {
        $this->resetActivoForm();
        $this->activoEditando = -1;
        $this->dispatch('abrir-modal-activo');
    }

    public function editarActivo(int $index): void
    {
        $activo = $this->activos[$index] ?? null;
        if (!$activo) {
            return;
        }
        $this->activoEditando = $index;
        $this->activoForm = array_merge($this->activoForm, $activo);

        // Mapeo de discos y monitores
        $this->activoForm['discos'] = $activo['almacenamiento_discos'] ?? [];
        $this->activoForm['monitores'] = $activo['monitores'] ?? [];

        $this->activoForm['software_checkboxes'] = [];
        $this->activoForm['software_otros'] = '';
        $opcionesSoftware = ['OFIMATICA', 'SAFYC', 'SIGEDOC', 'Diseño CAD', 'Anita', 'CorelDRAW', 'Chrome', 'ZIP/RAR'];
        $otros = [];
        foreach ($activo['software_instalado'] ?? [] as $sw) {
            if (in_array($sw, $opcionesSoftware)) {
                $this->activoForm['software_checkboxes'][] = $sw;
            } else {
                $otros[] = $sw;
            }
        }
        $this->activoForm['software_otros'] = implode(', ', $otros);

        $this->dispatch('abrir-modal-activo');
    }

    public function agregarDiscoServidor(): void
    {
        $this->srv_discos[] = ['tipo' => 'ssd', 'capacidad' => ''];
    }

    public function quitarDiscoServidor(int $index): void
    {
        array_splice($this->srv_discos, $index, 1);
    }

    public function agregarDiscoActivo(): void
    {
        $this->activoForm['discos'][] = ['tipo' => 'ssd', 'capacidad' => ''];
    }

    public function quitarDiscoActivo(int $index): void
    {
        array_splice($this->activoForm['discos'], $index, 1);
    }

    public function agregarMonitorActivo(): void
    {
        $this->activoForm['monitores'][] = ['marca' => '', 'modelo' => '', 'pulgadas' => '', 'conexion' => 'hdmi'];
    }

    public function quitarMonitorActivo(int $index): void
    {
        array_splice($this->activoForm['monitores'], $index, 1);
    }

    public function guardarActivo(): void
    {
        if (!$this->relevamientoId) {
            $this->guardarDatosGenerales();
        }

        // Procesar software
        $software = $this->activoForm['software_checkboxes'] ?? [];
        $otrosSw = array_filter(array_map('trim', explode(',', $this->activoForm['software_otros'] ?? '')));
        $software = array_merge($software, $otrosSw);

        $data = array_merge($this->activoForm, [
            'relevamiento_id' => $this->relevamientoId,
            'software_instalado' => array_values($software),
            'almacenamiento_discos' => $this->activoForm['discos'] ?? [],
            'monitores' => $this->activoForm['monitores'] ?? [],
        ]);
        unset($data['software_checkboxes'], $data['software_otros'], $data['discos'], $data['almacenamiento_gb'], $data['monitor_marca'], $data['monitor_modelo'], $data['monitor_pulgadas'], $data['monitor_conexion']);

        if (!($data['tiene_mouse'] ?? false)) {
            $data['mouse_marca'] = null;
            $data['mouse_modelo'] = null;
            $data['mouse_conexion'] = null;
        }
        if (!($data['tiene_teclado'] ?? false)) {
            $data['teclado_marca'] = null;
            $data['teclado_modelo'] = null;
            $data['teclado_conexion'] = null;
        }
        if (!($data['tiene_camara'] ?? false)) {
            $data['camara_marca'] = null;
            $data['camara_modelo'] = null;
            $data['camara_conexion'] = null;
        }
        if (!($data['tiene_parlantes'] ?? false)) {
            $data['parlantes_marca'] = null;
            $data['parlantes_modelo'] = null;
        }
        if (!($data['tiene_estabilizador'] ?? false)) {
            $data['estabilizador_marca'] = null;
            $data['estabilizador_modelo'] = null;
            $data['estabilizador_color'] = null;
        }

        if ($this->activoEditando >= 0 && isset($this->activos[$this->activoEditando]['id'])) {
            // Update en DB
            Activo::find($this->activos[$this->activoEditando]['id'])?->update($data);
        } else {
            Activo::create($data);
        }

        // Recargar activos desde DB
        $this->recargarActivos();
        $this->resetActivoForm();
        $this->activoEditando = -1;
        $this->dispatch('cerrar-modal-activo');
        $this->dispatch('activo-guardado');
    }

    public function eliminarActivo(int $index): void
    {
        $activo = $this->activos[$index] ?? null;
        if ($activo && isset($activo['id'])) {
            Activo::find($activo['id'])?->delete();
        }
        $this->recargarActivos();
    }

    private function recargarActivos(): void
    {
        if ($this->relevamientoId) {
            $this->activos = Activo::where('relevamiento_id', $this->relevamientoId)->get()->map(fn($a) => $a->toArray())->toArray();
        }
    }

    // ─── Equipos adicionales ───────────────────────────────────────
    public function agregarOtroEquipo(): void
    {
        if ($this->eq_otro_desc) {
            $this->eq_otros[] = [
                'descripcion' => $this->eq_otro_desc,
                'id_inventario' => $this->eq_otro_id,
            ];
            $this->eq_otro_desc = '';
            $this->eq_otro_id = '';
        }
    }

    public function quitarOtroEquipo(int $index): void
    {
        array_splice($this->eq_otros, $index, 1);
    }

    public function guardarEquiposAdicionales(): void
    {
        if (!$this->relevamientoId) {
            return;
        }

        EquipoAdicional::updateOrCreate(
            ['relevamiento_id' => $this->relevamientoId],
            [
                'relevamiento_id' => $this->relevamientoId,
                'tiene_proyector' => $this->eq_tiene_proyector,
                'proyector_marca' => $this->eq_proyector_marca ?: null,
                'proyector_conexion' => $this->eq_proyector_conexion ?: null,
                'proyector_id_inventario' => $this->eq_proyector_id ?: null,
                'tiene_led' => $this->eq_tiene_led,
                'led_marca' => $this->eq_led_marca ?: null,
                'led_pulgadas' => $this->eq_led_pulgadas ?: null,
                'led_conexion' => $this->eq_led_conexion ?: null,
                'led_id_inventario' => $this->eq_led_id ?: null,
                'tiene_camara' => $this->eq_tiene_camara,
                'camara_marca' => $this->eq_camara_marca ?: null,
                'camara_conexion' => $this->eq_camara_conexion ?: null,
                'camara_id_inventario' => $this->eq_camara_id ?: null,
                'cantidad_camaras_vigilancia' => $this->eq_cantidad_vigilancia,
                'camaras_vigilancia_ids' => array_filter(array_map('trim', explode(',', $this->eq_vigilancia_ids))),
                'otros_dispositivos' => $this->eq_otros,
            ],
        );
    }

    // ─── Finalizar ─────────────────────────────────────────────────
    public function finalizar(): void
    {
        $this->guardarEquiposAdicionales();

        if ($this->relevamientoId) {
            Relevamiento::find($this->relevamientoId)?->update(['estado' => 'completado']);
        }

        $this->dispatch('relevamiento-completado', id: $this->relevamientoId);
        session()->flash('success', 'Relevamiento guardado correctamente.');
        $this->redirectRoute('relevamientos.index');
    }

    // ─── Exportar EXCEL ─────────────────────────────────────────────
    public function exportarExcel(): void
    {
        if (!$this->relevamientoId) {
            return;
        }

        $r = Relevamiento::with(['area', 'oficina', 'servidor', 'activos', 'equiposAdicionales'])->find($this->relevamientoId);

        // Hoja 1: General e Infraestructura
        // Hoja 1: General e Infraestructura
        $general = [['<style bgcolor="#E2E8F0"><b>RELEVAMIENTO DE EQUIPAMIENTO INFORMÁTICO</b></style>'], [''], ['<b>DATOS GENERALES</b>'], ['Área', $r->area?->nombre], ['Oficina', $r->oficina?->nombre], ['Responsable', $r->responsable_nombre], ['Fecha', $r->created_at->format('d/m/Y')], [''], ['<b>INFRAESTRUCTURA - IMPRESORAS</b>'], ['Tiene Impresoras', $r->tiene_impresora ? 'SÍ' : 'NO']];

        if ($r->tiene_impresora && $r->impresoras) {
            foreach ($r->impresoras as $idx => $imp) {
                $num = $idx + 1;
                $general[] = ["Impresora #$num", $imp['marca'] . ' ' . $imp['modelo'] . ' (' . $imp['tipo'] . ') ' . ($imp['escaner'] ? 'w/ Escáner' : '')];
            }
        }

        if ($srv = $r->servidor) {
            $general[] = [''];
            $general[] = ['<b>DATOS DEL SERVIDOR</b>'];
            $general[] = ['Función', $srv->funcion];
            $general[] = ['SO', $srv->sistema_operativo . ' ' . $srv->so_version];
            $general[] = ['Hardware', $srv->tipo_equipamiento];
            $general[] = ['RAM', $srv->ram_gb . ' GB ' . $srv->ram_tipo];
        }

        // Hoja 2: Activos
        $activosHeader = ['Código Inv.', 'Tipo', 'Estado', 'Marca', 'Modelo CPU', 'GHz', 'RAM GB', 'RAM Tipo', 'Placa Video', 'Discos', 'Monitores', 'Mouse', 'Teclado', 'Software', 'Usuario'];
        $activosData = [$activosHeader];
        foreach ($r->activos as $a) {
            $discosStr = collect($a->almacenamiento_discos)->map(fn($d) => $d['tipo'] . ': ' . $d['capacidad'] . 'GB')->implode(' | ');
            $monitoresStr = collect($a->monitores)->map(fn($m) => $m['marca'] . ' ' . $m['pulgadas'] . '"')->implode(' | ');
            $softwareStr = implode(', ', $a->software_instalado ?? []);

            $activosData[] = [$a->codigo_inventario, $a->tipo, $a->estado, $a->marca, $a->cpu_modelo, $a->cpu_ghz, $a->ram_gb, $a->ram_tipo, $a->placa_video, $discosStr, $monitoresStr, $a->tiene_mouse ? 'SÍ' : 'NO', $a->tiene_teclado ? 'SÍ' : 'NO', $softwareStr, $a->usuario_nombre . ' ' . $a->usuario_apellido];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($general, 'General e Infra');
        if (count($activosData) > 1) {
            $xlsx->addSheet($activosData, 'Activos');
        }

        $fileName = 'relevamiento_' . Str::slug($r->oficina?->nombre ?? 'export') . '_' . date('Ymd_His') . '.xlsx';
        $xlsxPath = storage_path('app/public/' . $fileName);
        $xlsx->saveAs($xlsxPath);

        $this->dispatch('descargar-excel', url: asset('storage/' . $fileName), name: $fileName);
    }
};

?>

<div class="min-h-screen bg-slate-50" x-data="{ modalActivo: false }" @abrir-modal-activo.window="modalActivo = true"
    @cerrar-modal-activo.window="modalActivo = false">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- HEADER                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-semibold text-slate-800">Relevamiento de Equipos</h1>
                    @if ($relevamientoId)
                        <p class="text-xs text-slate-400">#{{ $relevamientoId }} · Guardado automáticamente</p>
                    @endif
                </div>
            </div>

            @if ($relevamientoId)
                <button wire:click="exportarExcel"
                    class="text-xs px-3 py-1.5 rounded-md border border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 flex items-center gap-1.5 transition font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar Excel
                </button>
            @endif
        </div>

        {{-- Progress Steps --}}
        <div class="max-w-5xl mx-auto px-4 pb-4">
            <div class="flex items-center gap-0">
                @php
                    $steps = [
                        1 => [
                            'label' => 'Datos Generales',
                            'icon' =>
                                'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        ],
                        2 => [
                            'label' => 'Servidor e Impresoras',
                            'icon' =>
                                'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01',
                        ],
                        3 => [
                            'label' => 'Activos',
                            'icon' =>
                                'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                        ],
                        4 => [
                            'label' => 'Equipos Adicionales',
                            'icon' =>
                                'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z',
                        ],
                    ];
                @endphp
                @foreach ($steps as $num => $info)
                    <button wire:click="irAPaso({{ $num }})"
                        class="flex-1 flex flex-col items-center gap-1 py-1 group relative transition-all">
                        <div @class([
                            'w-8 h-8 rounded-full flex items-center justify-center transition-all text-xs font-semibold',
                            'bg-blue-600 text-white shadow-md' => $step === $num,
                            'bg-emerald-500 text-white' => $step > $num,
                            'bg-slate-200 text-slate-500 group-hover:bg-slate-300' => $step < $num,
                        ])>
                            @if ($step > $num)
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span @class([
                            'text-xs font-medium hidden sm:block',
                            'text-blue-600' => $step === $num,
                            'text-emerald-600' => $step > $num,
                            'text-slate-400' => $step < $num,
                        ])>{{ $info['label'] }}</span>
                    </button>
                    @if (!$loop->last)
                        <div @class([
                            'flex-1 h-px mt-4 transition-all',
                            'bg-emerald-400' => $step > $num,
                            'bg-slate-200' => $step <= $num,
                        ])></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="max-w-5xl mx-auto px-4 py-8">

        @if (session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ─────────────────────────────────────────────────────── --}}
        {{-- STEP 1: DATOS GENERALES                                 --}}
        {{-- ─────────────────────────────────────────────────────── --}}
        @if ($step === 1)
            <div class="space-y-6 animate-in fade-in duration-300">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h2 class="font-semibold text-slate-800">Datos Generales del Relevamiento</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Área --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Área <span
                                    class="text-red-500">*</span></label>
                            <select wire:model.live="area_id"
                                class="w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">— Seleccionar área —</option>
                                @foreach ($this->areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Oficina --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Oficina / Repartición <span
                                    class="text-red-500">*</span></label>
                            <select wire:model="oficina_id" @disabled(!$area_id)
                                class="w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition disabled:opacity-50">
                                <option value="">— Seleccionar oficina —</option>
                                @foreach ($this->oficinas as $oficina)
                                    <option value="{{ $oficina->id }}">{{ $oficina->nombre }}</option>
                                @endforeach
                            </select>
                            @error('oficina_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Responsable Nombre --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Responsable — Apellido y
                                Nombre <span class="text-red-500">*</span></label>
                            <input wire:model="responsable_nombre" type="text" placeholder="García Juan Pablo"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                            @error('responsable_nombre')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>



                    </div>
                </div>
            </div>
        @endif

        {{-- ─────────────────────────────────────────────────────── --}}
        {{-- STEP 2: SERVIDOR / INFRAESTRUCTURA                      --}}
        {{-- ─────────────────────────────────────────────────────── --}}
        @if ($step === 2)
            <div class="space-y-6 animate-in fade-in duration-300">

                {{-- Toggle tiene servidor --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold text-slate-800">¿La repartición tiene servidor?</h2>
                            <p class="text-sm text-slate-400 mt-0.5">Si no tiene, se omitirán los campos siguientes.</p>
                        </div>
                        <button type="button" wire:click="$toggle('tiene_servidor')" @class([
                            'relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                            'bg-blue-600' => $tiene_servidor,
                            'bg-slate-200' => !$tiene_servidor,
                        ])>
                            <span @class([
                                'inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform',
                                'translate-x-6' => $tiene_servidor,
                                'translate-x-1' => !$tiene_servidor,
                            ])></span>
                        </button>
                    </div>
                </div>

                @if ($tiene_servidor)
                    {{-- Datos del servidor --}}
                    <x-relevamiento.card title="Datos del Servidor" icon="server">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <x-relevamiento.input label="Función que cumple" wire="srv_funcion"
                                placeholder="Controlador de dominio, archivo..." />
                            <x-relevamiento.input label="Sistema Operativo" wire="srv_so"
                                placeholder="Windows Server, Ubuntu..." />
                            <x-relevamiento.input label="Versión del SO" wire="srv_so_version"
                                placeholder="2022, 22.04 LTS..." />
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Requerimientos
                                    especiales</label>
                                <textarea wire:model="srv_requerimientos" rows="3" placeholder="Componentes, configuraciones especiales..."
                                    class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition resize-none"></textarea>
                            </div>
                        </div>
                    </x-relevamiento.card>

                    {{-- Hardware --}}
                    <x-relevamiento.card title="Hardware" icon="cpu-chip">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                            <x-relevamiento.input label="Tipo de equipamiento" wire="srv_tipo_hw"
                                placeholder="Tower, Rack, Blade..." />
                            <x-relevamiento.input label="CPU (GHz)" wire="srv_cpu_ghz" type="number" step="0.1"
                                placeholder="3.6" />
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">RAM</label>
                                <div class="flex gap-2">
                                    <input wire:model="srv_ram_gb" type="number" placeholder="GB"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <select wire:model="srv_ram_tipo"
                                        class="w-24 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                        <option value="">Tipo</option>
                                        <option value="DDR2">DDR2</option>
                                        <option value="DDR3">DDR3</option>
                                        <option value="DDR4">DDR4</option>
                                        <option value="DDR5">DDR5</option>
                                    </select>
                                </div>
                            </div>
                            <x-relevamiento.input label="SWAP (GB)" wire="srv_swap_gb" type="number"
                                placeholder="4" />

                            <div class="col-span-full mt-2">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-slate-700">Discos de
                                        almacenamiento</label>
                                    <button type="button" wire:click="agregarDiscoServidor"
                                        class="text-xs px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition">
                                        + Agregar disco
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    @foreach ($srv_discos as $index => $disco)
                                        <div class="flex items-center gap-2"
                                            wire:key="srv-disco-{{ $index }}">
                                            <select wire:model="srv_discos.{{ $index }}.tipo"
                                                class="w-32 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                                <option value="">Tipo</option>
                                                <option value="HDD">HDD</option>
                                                <option value="SSD">SSD</option>
                                                <option value="M.2">M.2</option>
                                            </select>
                                            <input wire:model="srv_discos.{{ $index }}.capacidad"
                                                type="number" placeholder="Capacidad (GB)"
                                                class="flex-1 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                            <button type="button"
                                                wire:click="quitarDiscoServidor({{ $index }})"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                    @if (empty($srv_discos))
                                        <p class="text-sm text-slate-400 italic">No hay discos registrados.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </x-relevamiento.card>

                    {{-- Base de Datos --}}
                    <x-relevamiento.card title="Base de Datos" icon="circle-stack">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                            <x-relevamiento.input label="Motor de BD" wire="srv_motor_bd"
                                placeholder="MySQL, PostgreSQL, MSSQL..." />
                            <x-relevamiento.input label="Cantidad de bases" wire="srv_cantidad_bases" type="number"
                                placeholder="5" />
                            <x-relevamiento.input label="Tamaño total (GB)" wire="srv_tamano_bases" type="number"
                                placeholder="50" />
                        </div>
                    </x-relevamiento.card>

                    {{-- Usuarios --}}
                    <x-relevamiento.card title="Usuarios" icon="users">
                        <div class="grid grid-cols-2 gap-5">
                            <x-relevamiento.input label="Usuarios concurrentes" wire="srv_usuarios_concurrentes"
                                type="number" placeholder="20" />
                            <x-relevamiento.input label="Usuarios totales" wire="srv_usuarios_totales" type="number"
                                placeholder="100" />
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Estimación de
                                    crecimiento</label>
                                <textarea wire:model="srv_crecimiento" rows="2" placeholder="Ej: 20% anual de nuevos usuarios..."
                                    class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition resize-none"></textarea>
                            </div>
                        </div>
                    </x-relevamiento.card>

                    {{-- Licencias --}}
                    <x-relevamiento.card title="Licencias" icon="key">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                            <x-relevamiento.input label="Tipo de licencia" wire="srv_licencia_tipo"
                                placeholder="Anual, mensual, perpetua..." />
                            <x-relevamiento.input label="Cantidad" wire="srv_licencia_cantidad" type="number"
                                placeholder="10" />
                            <div class="col-span-full">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Observaciones</label>
                                <textarea wire:model="srv_licencia_obs" rows="2"
                                    class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition resize-none"></textarea>
                            </div>
                        </div>
                    </x-relevamiento.card>

                    {{-- Red --}}
                    <x-relevamiento.card title="Red" icon="wifi">
                        <div class="grid grid-cols-2 md:grid-cols-2 gap-5">
                            <x-relevamiento.input label="Red LAN" wire="srv_red_lan" placeholder="192.168.1.0/24" />
                            <x-relevamiento.input label="WAN" wire="srv_wan" placeholder="10.0.0.0/8" />
                            <x-relevamiento.input label="Internet dedicado" wire="srv_internet"
                                placeholder="100 Mbps fibra" />
                            <x-relevamiento.input label="Tráfico estimado (MB/mes)" wire="srv_trafico" type="number"
                                placeholder="10000" />
                        </div>
                    </x-relevamiento.card>

                @endif

                {{-- Impresoras --}}
                <x-relevamiento.card title="Impresoras de la Oficina" icon="printer">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-slate-700">¿Esta oficina tiene impresoras?</span>
                        <div class="flex items-center gap-4">
                            @if ($tiene_impresora)
                                <button type="button" wire:click="agregarImpresora"
                                    class="text-xs px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-medium hover:bg-blue-100 transition">
                                    + Agregar otra
                                </button>
                            @endif
                            <button type="button"
                                wire:click="$set('tiene_impresora', !{{ $tiene_impresora ? 'true' : 'false' }})"
                                @class([
                                    'relative inline-flex h-6 w-10 items-center rounded-full transition-colors',
                                    'bg-blue-600' => $tiene_impresora,
                                    'bg-slate-200' => !$tiene_impresora,
                                ])>
                                <span @class([
                                    'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                    'translate-x-5' => $tiene_impresora,
                                    'translate-x-1' => !$tiene_impresora,
                                ])></span>
                            </button>
                        </div>
                    </div>

                    @if ($tiene_impresora)
                        <div class="space-y-6">
                            @foreach ($impresoras as $index => $imp)
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 relative animate-in slide-in-from-top-2 duration-300"
                                    wire:key="impresora-{{ $index }}">
                                    <button type="button" wire:click="quitarImpresora({{ $index }})"
                                        class="absolute top-2 right-2 p-1 text-slate-400 hover:text-red-600 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Marca</label>
                                            <input wire:model="impresoras.{{ $index }}.marca" type="text"
                                                placeholder="HP, Brother..."
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Modelo</label>
                                            <input wire:model="impresoras.{{ $index }}.modelo" type="text"
                                                placeholder="LaserJet..."
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">IP</label>
                                            <input wire:model="impresoras.{{ $index }}.ip" type="text"
                                                placeholder="192.168.1..."
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Tipo</label>
                                            <select wire:model="impresoras.{{ $index }}.tipo"
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                                <option value="tinta_continua">Tinta Continua</option>
                                                <option value="toner">Tóner / Láser</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-500 mb-1">Conexión</label>
                                            <select wire:model="impresoras.{{ $index }}.conexion"
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                                <option value="red">Por Red</option>
                                                <option value="wifi">Wifi</option>
                                                <option value="usb">USB</option>
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-2 pt-5">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox"
                                                    wire:model="impresoras.{{ $index }}.escaner"
                                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                <span class="text-sm text-slate-700">¿Tiene Escáner?</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if (empty($impresoras))
                                <div class="text-center py-4 border-2 border-dashed border-slate-200 rounded-xl">
                                    <p class="text-sm text-slate-500">No hay impresoras cargadas. Hacé clic en "+
                                        Agregar
                                        otra".</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </x-relevamiento.card>

                {{-- Modems --}}
                <x-relevamiento.card title="Módems / Routers de la Oficina" icon="rss">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-slate-700">¿Esta oficina tiene módems o routers
                            propios?</span>
                        <div class="flex items-center gap-4">
                            @if ($tiene_modem)
                                <button type="button" wire:click="agregarModem"
                                    class="text-xs px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-medium hover:bg-blue-100 transition">
                                    + Agregar otro
                                </button>
                            @endif
                            <button type="button"
                                wire:click="$set('tiene_modem', !{{ $tiene_modem ? 'true' : 'false' }})"
                                @class([
                                    'relative inline-flex h-6 w-10 items-center rounded-full transition-colors',
                                    'bg-blue-600' => $tiene_modem,
                                    'bg-slate-200' => !$tiene_modem,
                                ])>
                                <span @class([
                                    'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                    'translate-x-5' => $tiene_modem,
                                    'translate-x-1' => !$tiene_modem,
                                ])></span>
                            </button>
                        </div>
                    </div>

                    @if ($tiene_modem)
                        <div class="space-y-6">
                            @foreach ($modems as $index => $modem)
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 relative animate-in slide-in-from-top-2 duration-300"
                                    wire:key="modem-{{ $index }}">
                                    <button type="button" wire:click="quitarModem({{ $index }})"
                                        class="absolute top-2 right-2 p-1 text-slate-400 hover:text-red-600 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Marca</label>
                                            <input wire:model="modems.{{ $index }}.marca" type="text"
                                                placeholder="TP-Link, Cisco..."
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Modelo</label>
                                            <input wire:model="modems.{{ $index }}.modelo" type="text"
                                                placeholder="Archer..."
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Nombre de Red
                                                (SSID)
                                            </label>
                                            <input wire:model="modems.{{ $index }}.ssid" type="text"
                                                placeholder="OFICINA_WIFI"
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Contraseña de
                                                Red</label>
                                            <input wire:model="modems.{{ $index }}.password" type="text"
                                                placeholder="********"
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">IP de
                                                Gestión</label>
                                            <input wire:model="modems.{{ $index }}.ip" type="text"
                                                placeholder="192.168.1.1"
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Dirección
                                                MAC</label>
                                            <input wire:model.blur="modems.{{ $index }}.mac" type="text"
                                                placeholder="AA:BB:CC..."
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition font-mono" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Proxy</label>
                                            <input wire:model="modems.{{ $index }}.proxy" type="text"
                                                placeholder="10.1.2.3:8080"
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Usuario
                                                (Gestión)</label>
                                            <input wire:model="modems.{{ $index }}.usuario" type="text"
                                                placeholder="admin"
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Contraseña
                                                (Gestión)</label>
                                            <input wire:model="modems.{{ $index }}.clave" type="text"
                                                placeholder="********"
                                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if (empty($modems))
                                <div class="text-center py-4 border-2 border-dashed border-slate-200 rounded-xl">
                                    <p class="text-sm text-slate-500">No hay módems cargados. Hacé clic en "+ Agregar
                                        otro".</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </x-relevamiento.card>
            </div>
        @endif

        {{-- ─────────────────────────────────────────────────────── --}}
        {{-- STEP 3: ACTIVOS                                         --}}
        {{-- ─────────────────────────────────────────────────────── --}}
        @if ($step === 3)
            <div class="space-y-6 animate-in fade-in duration-300">

                {{-- Toolbar --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Activos registrados</h2>
                        <p class="text-sm text-slate-400">{{ count($activos) }} equipo(s) en esta oficina</p>
                    </div>
                    <button wire:click="nuevoActivo"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Agregar equipo
                    </button>
                </div>

                {{-- Lista de activos --}}
                @forelse($activos as $i => $activo)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
                        <div @class([
                            'w-12 h-12 rounded-xl flex items-center justify-center shrink-0',
                            'bg-blue-50' => ($activo['tipo'] ?? '') === 'desktop',
                            'bg-purple-50' => ($activo['tipo'] ?? '') === 'notebook',
                            'bg-orange-50' => ($activo['tipo'] ?? '') === 'thin_client',
                        ])>
                            @if (($activo['tipo'] ?? '') === 'notebook')
                                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-slate-800">
                                    {{ $activo['marca'] ?? '—' }}
                                </span>
                                <span @class([
                                    'text-xs px-2 py-0.5 rounded-full font-medium',
                                    'bg-emerald-100 text-emerald-700' => ($activo['estado'] ?? '') === 'activo',
                                    'bg-red-100 text-red-700' => ($activo['estado'] ?? '') === 'inactivo',
                                    'bg-amber-100 text-amber-700' => ($activo['estado'] ?? '') === 'reparacion',
                                ])>{{ ucfirst($activo['estado'] ?? 'activo') }}</span>
                            </div>
                            <div class="text-sm text-slate-500 mt-0.5 truncate">
                                {{ $activo['so_tipo'] ?? '—' }} {{ $activo['so_version'] ?? '' }} ·
                                RAM: {{ $activo['ram_gb'] ?? '?' }} GB {{ $activo['ram_tipo'] ?? '' }} ·
                                @if ($activo['usuario_apellido'] ?? false)
                                    Usuario: {{ $activo['usuario_apellido'] }}, {{ $activo['usuario_nombre'] ?? '' }}
                                @else
                                    Sin usuario asignado
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="editarActivo({{ $i }})"
                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="eliminarActivo({{ $i }})"
                                wire:confirm="¿Eliminar este equipo?"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-slate-500 font-medium">Sin equipos registrados</p>
                        <p class="text-sm text-slate-400 mt-1">Agregá el primer equipo con el botón de arriba</p>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- ─────────────────────────────────────────────────────── --}}
        {{-- STEP 4: EQUIPOS ADICIONALES                             --}}
        {{-- ─────────────────────────────────────────────────────── --}}
        @if ($step === 4)
            <div class="space-y-6 animate-in fade-in duration-300">

                {{-- Proyector --}}
                <x-relevamiento.card title="Proyector" icon="video-camera">
                    <div class="flex items-center gap-3 mb-4">
                        <button type="button" wire:click="$toggle('eq_tiene_proyector')"
                            @class([
                                'relative inline-flex h-7 w-12 items-center rounded-full transition-colors',
                                'bg-blue-600' => $eq_tiene_proyector,
                                'bg-slate-200' => !$eq_tiene_proyector,
                            ])>
                            <span @class([
                                'inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform',
                                'translate-x-6' => $eq_tiene_proyector,
                                'translate-x-1' => !$eq_tiene_proyector,
                            ])></span>
                        </button>
                        <span
                            class="text-sm text-slate-600">{{ $eq_tiene_proyector ? 'Sí tiene proyector' : 'No tiene proyector' }}</span>
                    </div>
                    @if ($eq_tiene_proyector)
                        <div class="grid grid-cols-3 gap-4">
                            <x-relevamiento.input label="Marca" wire="eq_proyector_marca" />
                            <x-relevamiento.select label="Conexión" wire="eq_proyector_conexion" :options="['usb' => 'USB', 'inalambrico' => 'Inalámbrico', 'hdmi' => 'HDMI']" />
                            <x-relevamiento.input label="ID Inventario" wire="eq_proyector_id" />
                        </div>
                    @endif
                </x-relevamiento.card>

                {{-- Pantalla LED --}}
                <x-relevamiento.card title="Pantalla LED" icon="tv">
                    <div class="flex items-center gap-3 mb-4">
                        <button type="button" wire:click="$toggle('eq_tiene_led')" @class([
                            'relative inline-flex h-7 w-12 items-center rounded-full transition-colors',
                            'bg-blue-600' => $eq_tiene_led,
                            'bg-slate-200' => !$eq_tiene_led,
                        ])>
                            <span @class([
                                'inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform',
                                'translate-x-6' => $eq_tiene_led,
                                'translate-x-1' => !$eq_tiene_led,
                            ])></span>
                        </button>
                        <span
                            class="text-sm text-slate-600">{{ $eq_tiene_led ? 'Sí tiene pantalla LED' : 'No tiene pantalla LED' }}</span>
                    </div>
                    @if ($eq_tiene_led)
                        <div class="grid grid-cols-4 gap-4">
                            <x-relevamiento.input label="Marca" wire="eq_led_marca" />
                            <x-relevamiento.input label="Pulgadas" wire="eq_led_pulgadas" type="number" />
                            <x-relevamiento.select label="Conexión" wire="eq_led_conexion" :options="['usb' => 'USB', 'inalambrico' => 'Inalámbrico', 'hdmi' => 'HDMI']" />
                            <x-relevamiento.input label="ID Inventario" wire="eq_led_id" />
                        </div>
                    @endif
                </x-relevamiento.card>

                {{-- Cámara videoconferencia --}}
                <x-relevamiento.card title="Cámara (Videoconferencia)" icon="camera">
                    <div class="flex items-center gap-3 mb-4">
                        <button type="button" wire:click="$toggle('eq_tiene_camara')" @class([
                            'relative inline-flex h-7 w-12 items-center rounded-full transition-colors',
                            'bg-blue-600' => $eq_tiene_camara,
                            'bg-slate-200' => !$eq_tiene_camara,
                        ])>
                            <span @class([
                                'inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform',
                                'translate-x-6' => $eq_tiene_camara,
                                'translate-x-1' => !$eq_tiene_camara,
                            ])></span>
                        </button>
                        <span
                            class="text-sm text-slate-600">{{ $eq_tiene_camara ? 'Sí tiene cámara' : 'No tiene cámara' }}</span>
                    </div>
                    @if ($eq_tiene_camara)
                        <div class="grid grid-cols-3 gap-4">
                            <x-relevamiento.input label="Marca" wire="eq_camara_marca" />
                            <x-relevamiento.select label="Conexión" wire="eq_camara_conexion" :options="['usb' => 'USB', 'inalambrica' => 'Inalámbrica']" />
                            <x-relevamiento.input label="ID Inventario" wire="eq_camara_id" />
                        </div>
                    @endif
                </x-relevamiento.card>

                {{-- Cámaras de Vigilancia --}}
                <x-relevamiento.card title="Cámaras de Vigilancia" icon="eye">
                    <div class="grid grid-cols-2 gap-4">
                        <x-relevamiento.input label="Cantidad" wire="eq_cantidad_vigilancia" type="number" />
                        <x-relevamiento.input label="IDs (separados por coma)" wire="eq_vigilancia_ids"
                            placeholder="CAM-01, CAM-02..." />
                    </div>
                </x-relevamiento.card>

                {{-- Otros Dispositivos --}}
                <x-relevamiento.card title="Otros Dispositivos" icon="puzzle-piece">
                    <div class="flex gap-3 mb-4">
                        <input wire:model="eq_otro_desc" type="text" placeholder="Descripción del dispositivo"
                            class="flex-1 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        <input wire:model="eq_otro_id" type="text" placeholder="ID inventario"
                            class="w-36 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        <button wire:click="agregarOtroEquipo"
                            class="px-3 py-2 bg-blue-600 text-white rounded-xl text-sm hover:bg-blue-700 transition">
                            Agregar
                        </button>
                    </div>
                    <div class="space-y-2">
                        @foreach ($eq_otros as $j => $otro)
                            <div
                                class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div>
                                    <span class="text-sm font-medium text-slate-700">{{ $otro['descripcion'] }}</span>
                                    @if ($otro['id_inventario'])
                                        <span class="text-xs text-slate-400 ml-2">·
                                            {{ $otro['id_inventario'] }}</span>
                                    @endif
                                </div>
                                <button wire:click="quitarOtroEquipo({{ $j }})"
                                    class="p-1 text-slate-400 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </x-relevamiento.card>
            </div>
        @endif

        {{-- ─────────────────────────────────────────────────────── --}}
        {{-- NAV BUTTONS                                             --}}
        {{-- ─────────────────────────────────────────────────────── --}}
        <div class="mt-8 flex items-center justify-between pt-6 border-t border-slate-200">
            @if ($step > 1)
                <button wire:click="pasoAnterior"
                    class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-medium hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Anterior
                </button>
            @else
                <div></div>
            @endif

            @if ($step < $totalSteps)
                <button wire:click="siguientePaso"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                    Siguiente
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <button wire:click="finalizar"
                    class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Finalizar relevamiento
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: FORMULARIO DE ACTIVO                                   --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="modalActivo" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">

        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div
                class="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between z-10">
                <h3 class="font-semibold text-slate-800">
                    {{ $activoEditando >= 0 ? 'Editar equipo' : 'Nuevo equipo' }}
                </h3>
                <button @click="modalActivo = false"
                    class="p-2 text-slate-400 hover:text-slate-600 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6">

                {{-- Identificación --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Identificación</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Código inventario</label>
                            <input wire:model="activoForm.codigo_inventario" type="text"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
                            <select wire:model="activoForm.tipo"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="desktop">Computadora</option>
                                <option value="notebook">Notebook</option>
                                <option value="thin_client">Cliente Virtual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                            <select wire:model="activoForm.estado"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="reparacion">En reparación</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Hardware --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Hardware</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Marca (Gabinete/PC)</label>
                            <input wire:model="activoForm.marca" type="text" placeholder="HP, Dell, Lenovo..."
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                        <div class="col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Modelo
                                CPU</label>
                            <input wire:model="activoForm.cpu_modelo" type="text"
                                placeholder="Intel Core i5-12400..."
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">CPU (GHz)</label>
                            <input wire:model="activoForm.cpu_ghz" type="number" step="0.1" placeholder="3.2"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                        <div class="col-span-2"><label
                                class="block text-sm font-medium text-slate-700 mb-1">RAM</label>
                            <div class="flex gap-2">
                                <input wire:model="activoForm.ram_gb" type="number" placeholder="GB"
                                    class="flex-1 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                <select wire:model="activoForm.ram_tipo"
                                    class="w-32 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                    <option value="">Tipo</option>
                                    <option value="DDR2">DDR2</option>
                                    <option value="DDR3">DDR3</option>
                                    <option value="DDR4">DDR4</option>
                                    <option value="DDR5">DDR5</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-span-full"><label class="block text-sm font-medium text-slate-700 mb-1">Placa
                                de
                                video</label>
                            <input wire:model="activoForm.placa_video" type="text"
                                placeholder="Integrada / NVIDIA GTX..."
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>

                        <div class="col-span-full mt-2 border-t border-slate-100 pt-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-slate-700">Discos de
                                    almacenamiento</label>
                                <button type="button" wire:click="agregarDiscoActivo"
                                    class="text-xs px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition">
                                    + Agregar disco
                                </button>
                            </div>
                            <div class="space-y-2">
                                @foreach ($activoForm['discos'] as $index => $disco)
                                    <div class="flex items-center gap-2" wire:key="activo-disco-{{ $index }}">
                                        <select wire:model="activoForm.discos.{{ $index }}.tipo"
                                            class="w-32 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                            <option value="">Tipo</option>
                                            <option value="HDD">HDD</option>
                                            <option value="SSD">SSD</option>
                                            <option value="M.2">M.2</option>
                                        </select>
                                        <input wire:model="activoForm.discos.{{ $index }}.capacidad"
                                            type="number" placeholder="Capacidad (GB)"
                                            class="flex-1 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                        <button type="button" wire:click="quitarDiscoActivo({{ $index }})"
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                                @if (empty($activoForm['discos']))
                                    <p class="text-sm text-slate-400 italic">No hay discos registrados.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Red y SO --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Red y Sistema
                        Operativo</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Tipo de red</label>
                            <select wire:model="activoForm.tipo_red"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="ip_fija">IP Fija</option>
                                <option value="dhcp">DHCP</option>
                                <option value="wifi">WiFi</option>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">IP</label>
                            <input wire:model="activoForm.ip" type="text" placeholder="192.168.1.100"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">MAC</label>
                            <input wire:model="activoForm.mac" type="text" placeholder="00:1A:2B:3C:4D:5E"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">SO</label>
                            <select wire:model.live="activoForm.so_tipo"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Seleccionar...</option>
                                <option value="Windows">Windows</option>
                                <option value="Linux">Linux</option>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Versión</label>
                            <select wire:model="activoForm.so_version"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Seleccionar...</option>
                                @if (($activoForm['so_tipo'] ?? '') === 'Windows')
                                    <option value="7 pro">7 pro</option>
                                    <option value="10 pro">10 pro</option>
                                    <option value="11 pro">11 pro</option>
                                @elseif(($activoForm['so_tipo'] ?? '') === 'Linux')
                                    <option value="Suse">Suse</option>
                                    <option value="Ubuntu">Ubuntu</option>
                                    <option value="Otro">Otro</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Monitores --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Monitores</h4>
                        <button type="button" wire:click="agregarMonitorActivo"
                            class="text-xs px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition">
                            + Agregar monitor
                        </button>
                    </div>
                    <div class="space-y-3">
                        @foreach ($activoForm['monitores'] as $index => $monitor)
                            <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl"
                                wire:key="activo-monitor-{{ $index }}">
                                <input wire:model="activoForm.monitores.{{ $index }}.marca" type="text"
                                    placeholder="Marca"
                                    class="w-1/4 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                <input wire:model="activoForm.monitores.{{ $index }}.modelo" type="text"
                                    placeholder="Modelo"
                                    class="w-1/4 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                <input wire:model="activoForm.monitores.{{ $index }}.pulgadas" type="number"
                                    step="0.1" placeholder="Pulgadas"
                                    class="w-1/4 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                <select wire:model="activoForm.monitores.{{ $index }}.conexion"
                                    class="w-1/4 rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                    <option value="hdmi">HDMI</option>
                                    <option value="vga">VGA</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <button type="button" wire:click="quitarMonitorActivo({{ $index }})"
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                        @if (empty($activoForm['monitores']))
                            <p class="text-sm text-slate-400 italic">No hay monitores registrados. Haz clic en "Agregar
                                monitor".</p>
                        @endif
                    </div>
                </div>

                {{-- Periféricos Básicos (Mouse/Teclado) --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Periféricos Básicos
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Mouse --}}
                        <div class="p-4 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-slate-700">Mouse</span>
                                <button type="button"
                                    wire:click="$set('activoForm.tiene_mouse', !{{ $activoForm['tiene_mouse'] ? 'true' : 'false' }})"
                                    @class([
                                        'relative inline-flex h-6 w-10 items-center rounded-full transition-colors',
                                        'bg-blue-600' => $activoForm['tiene_mouse'],
                                        'bg-slate-200' => !$activoForm['tiene_mouse'],
                                    ])>
                                    <span @class([
                                        'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                        'translate-x-5' => $activoForm['tiene_mouse'],
                                        'translate-x-1' => !$activoForm['tiene_mouse'],
                                    ])></span>
                                </button>
                            </div>
                            @if ($activoForm['tiene_mouse'])
                                <div class="space-y-2">
                                    <input wire:model="activoForm.mouse_marca" type="text" placeholder="Marca"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <input wire:model="activoForm.mouse_modelo" type="text" placeholder="Modelo"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <select wire:model="activoForm.mouse_conexion"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                        <option value="usb">USB</option>
                                        <option value="inalambrico">Inalámbrico</option>
                                    </select>
                                </div>
                            @endif
                        </div>

                        {{-- Teclado --}}
                        <div class="p-4 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-slate-700">Teclado</span>
                                <button type="button"
                                    wire:click="$set('activoForm.tiene_teclado', !{{ $activoForm['tiene_teclado'] ? 'true' : 'false' }})"
                                    @class([
                                        'relative inline-flex h-6 w-10 items-center rounded-full transition-colors',
                                        'bg-blue-600' => $activoForm['tiene_teclado'],
                                        'bg-slate-200' => !$activoForm['tiene_teclado'],
                                    ])>
                                    <span @class([
                                        'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                        'translate-x-5' => $activoForm['tiene_teclado'],
                                        'translate-x-1' => !$activoForm['tiene_teclado'],
                                    ])></span>
                                </button>
                            </div>
                            @if ($activoForm['tiene_teclado'])
                                <div class="space-y-2">
                                    <input wire:model="activoForm.teclado_marca" type="text" placeholder="Marca"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <input wire:model="activoForm.teclado_modelo" type="text" placeholder="Modelo"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <select wire:model="activoForm.teclado_conexion"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                        <option value="usb">USB</option>
                                        <option value="inalambrico">Inalámbrico</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Impresora removida de aquí --}}

                {{-- Otros dispositivos --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Otros dispositivos
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-4 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-slate-700">Cámara</span>
                                <button type="button"
                                    wire:click="$set('activoForm.tiene_camara', !{{ $activoForm['tiene_camara'] ? 'true' : 'false' }})"
                                    @class([
                                        'relative inline-flex h-6 w-10 items-center rounded-full transition-colors',
                                        'bg-blue-600' => $activoForm['tiene_camara'],
                                        'bg-slate-200' => !$activoForm['tiene_camara'],
                                    ])>
                                    <span @class([
                                        'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                        'translate-x-5' => $activoForm['tiene_camara'],
                                        'translate-x-1' => !$activoForm['tiene_camara'],
                                    ])></span>
                                </button>
                            </div>
                            @if ($activoForm['tiene_camara'])
                                <div class="space-y-2">
                                    <input wire:model="activoForm.camara_marca" type="text" placeholder="Marca"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <input wire:model="activoForm.camara_modelo" type="text" placeholder="Modelo"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <select wire:model="activoForm.camara_conexion"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                        <option value="usb">USB</option>
                                        <option value="inalambrica">Inalámbrica</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-slate-700">Parlantes</span>
                                <button type="button"
                                    wire:click="$set('activoForm.tiene_parlantes', !{{ $activoForm['tiene_parlantes'] ? 'true' : 'false' }})"
                                    @class([
                                        'relative inline-flex h-6 w-10 items-center rounded-full transition-colors',
                                        'bg-blue-600' => $activoForm['tiene_parlantes'],
                                        'bg-slate-200' => !$activoForm['tiene_parlantes'],
                                    ])>
                                    <span @class([
                                        'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                        'translate-x-5' => $activoForm['tiene_parlantes'],
                                        'translate-x-1' => !$activoForm['tiene_parlantes'],
                                    ])></span>
                                </button>
                            </div>
                            @if ($activoForm['tiene_parlantes'])
                                <div class="space-y-2">
                                    <input wire:model="activoForm.parlantes_marca" type="text" placeholder="Marca"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <input wire:model="activoForm.parlantes_modelo" type="text"
                                        placeholder="Modelo"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                </div>
                            @endif
                        </div>
                        <div class="p-4 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-slate-700">Estabilizador</span>
                                <button type="button"
                                    wire:click="$set('activoForm.tiene_estabilizador', !{{ isset($activoForm['tiene_estabilizador']) && $activoForm['tiene_estabilizador'] ? 'true' : 'false' }})"
                                    @class([
                                        'relative inline-flex h-6 w-10 items-center rounded-full transition-colors',
                                        'bg-blue-600' => $activoForm['tiene_estabilizador'] ?? false,
                                        'bg-slate-200' => !($activoForm['tiene_estabilizador'] ?? false),
                                    ])>
                                    <span @class([
                                        'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                                        'translate-x-5' => $activoForm['tiene_estabilizador'] ?? false,
                                        'translate-x-1' => !($activoForm['tiene_estabilizador'] ?? false),
                                    ])></span>
                                </button>
                            </div>
                            @if ($activoForm['tiene_estabilizador'] ?? false)
                                <div class="space-y-2">
                                    <input wire:model="activoForm.estabilizador_marca" type="text"
                                        placeholder="Marca"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <input wire:model="activoForm.estabilizador_modelo" type="text"
                                        placeholder="Modelo"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                    <input wire:model="activoForm.estabilizador_color" type="text"
                                        placeholder="Color"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Software --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Software instalado
                    </h4>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach (['OFIMATICA', 'SAFYC', 'SIGEDOC', 'Diseño CAD'] as $sw)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="activoForm.software_checkboxes"
                                        value="{{ $sw }}"
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-700">{{ $sw }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Otros programas (separados por
                                coma)</label>
                            <input wire:model="activoForm.software_otros" type="text"
                                placeholder="Ej: Photoshop, SIGEDOC..."
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                    </div>
                </div>

                {{-- Usuario asignado --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Usuario asignado
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Apellido</label>
                            <input wire:model="activoForm.usuario_apellido" type="text"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                            <input wire:model="activoForm.usuario_nombre" type="text"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition" />
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Carácter</label>
                            <select wire:model="activoForm.usuario_caracter"
                                class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">—</option>
                                <option value="titular">Titular</option>
                                <option value="interino">Interino</option>
                                <option value="contratado">Contratado</option>
                                <option value="suplente">Suplente</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="sticky bottom-0 bg-white border-t border-slate-100 px-6 py-4 flex justify-end gap-3">
                <button @click="modalActivo = false"
                    class="px-4 py-2 text-sm text-slate-600 border border-slate-300 rounded-xl hover:bg-slate-50 transition">
                    Cancelar
                </button>
                <button wire:click="guardarActivo"
                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition shadow-sm">
                    {{ $activoEditando >= 0 ? 'Guardar cambios' : 'Agregar equipo' }}
                </button>
            </div>
        </div>
    </div>

    {{-- Export Download script --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('descargar-excel', ({
                url,
                name
            }) => {
                const a = document.createElement('a');
                a.href = url;
                a.download = name;
                a.click();
            });
        });
    </script>
</div>
