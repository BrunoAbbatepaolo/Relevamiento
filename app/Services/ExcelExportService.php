<?php

namespace App\Services;

use App\Models\Relevamiento;
use Shuchkin\SimpleXLSXGen;

class ExcelExportService
{
    /**
     * Genera un archivo Excel con toda la base de datos de relevamientos y sus relaciones.
     *
     * @param string $path Ruta donde se guardará el archivo.
     * @return void
     */
    public static function exportTo(string $path): void
    {
        // Cargar todos los datos con sus relaciones precargadas
        $relevamientos = Relevamiento::with(['area', 'oficina', 'user', 'servidor', 'activos', 'equiposAdicionales'])->get();

        // 1. Construir la pestaña: Relevamientos
        $sheetRelevamientos = [
            [
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>ID Relevamiento</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Área</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Oficina</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Responsable</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Estado</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Cargado Por (Usuario)</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Tiene Impresoras</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Cant. Impresoras</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Tiene Módems</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Cant. Módems</b></style>',
                '<style bgcolor="#3B82F6" color="#FFFFFF"><b>Fecha Creación</b></style>'
            ]
        ];

        // 2. Construir la pestaña: Activos (Equipos)
        $sheetActivos = [
            [
                '<style bgcolor="#10B981" color="#FFFFFF"><b>ID Activo</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>ID Relevamiento</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Oficina</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Responsable</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Código Inventario</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Tipo</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Estado</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Tipo Red</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>IP</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>MAC</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Sistema Operativo</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>SO Versión</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Marca</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Modelo</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>CPU Modelo</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>CPU GHz</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>RAM GB</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>RAM Tipo</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Placa Video</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Motherboard</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Discos</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Monitores</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Mouse</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Mouse Info</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Teclado</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Teclado Info</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Estabilizador</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Estabilizador Info</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Cámara</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Cámara Info</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Parlantes</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Parlantes Info</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Software Instalado</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Usuario Nombre</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Usuario Apellido</b></style>',
                '<style bgcolor="#10B981" color="#FFFFFF"><b>Usuario Carácter</b></style>'
            ]
        ];

        // 3. Construir la pestaña: Servidores
        $sheetServidores = [
            [
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>ID Servidor</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>ID Relevamiento</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Oficina</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Responsable</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Función</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Sistema Operativo</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>SO Versión</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Arquitectura</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Requerimientos Especiales</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>CPU Modelo</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>CPU GHz</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>RAM GB</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>RAM Tipo</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>SWAP GB</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Almacenamiento GB</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Discos</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Motor BD</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Cant. Bases</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Tamaño Bases GB</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Usuarios Concurrentes</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Usuarios Totales</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Estimación Crecimiento</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Licencia Tipo</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Licencia Cantidad</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Licencia Observaciones</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Red LAN</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>WAN</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Internet Dedicado</b></style>',
                '<style bgcolor="#EF4444" color="#FFFFFF"><b>Tráfico Estimado (MB)</b></style>'
            ]
        ];

        // 4. Construir la pestaña: Equipos Adicionales
        $sheetAdicionales = [
            [
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>ID Eq. Adicional</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>ID Relevamiento</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Oficina</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Responsable</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Proyector</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Proyector Marca/Conexión</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Proyector Inv.</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>LED</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>LED Marca/Pulgadas/Conexión</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>LED Inv.</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Cámara Conf.</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Cámara Conf. Marca/Conexión</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Cámara Conf. Inv.</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Cant. Cámaras Vigilancia</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Cámaras Vigilancia IDs</b></style>',
                '<style bgcolor="#F59E0B" color="#FFFFFF"><b>Otros Dispositivos</b></style>'
            ]
        ];

        // 5. Construir la pestaña: Impresoras
        $sheetImpresoras = [
            [
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>ID Relevamiento</b></style>',
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>Oficina</b></style>',
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>Responsable</b></style>',
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>Marca</b></style>',
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>Modelo</b></style>',
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>IP</b></style>',
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>Conexión</b></style>',
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>Tiene Escáner</b></style>',
                '<style bgcolor="#8B5CF6" color="#FFFFFF"><b>Tipo (Toner/Tinta)</b></style>'
            ]
        ];

        // 6. Construir la pestaña: Modems
        $sheetModems = [
            [
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>ID Relevamiento</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>Oficina</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>Responsable</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>Marca</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>Modelo</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>SSID</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>Password</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>IP</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>Proxy</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>Usuario</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>Clave</b></style>',
                '<style bgcolor="#EC4899" color="#FFFFFF"><b>MAC</b></style>'
            ]
        ];

        // Llenar datos iterando los relevamientos
        foreach ($relevamientos as $r) {
            $oficinaNombre = $r->oficina?->nombre ?? 'N/A';
            $areaNombre = $r->area?->nombre ?? 'N/A';

            // Pestaña Relevamientos
            $sheetRelevamientos[] = [
                $r->id,
                $areaNombre,
                $oficinaNombre,
                $r->responsable_nombre,
                $r->estado,
                $r->user?->name ?? 'N/A',
                $r->tiene_impresora ? 'SÍ' : 'NO',
                count($r->impresoras ?? []),
                $r->tiene_modem ? 'SÍ' : 'NO',
                count($r->modems ?? []),
                $r->created_at?->format('d/m/Y H:i')
            ];

            // Pestaña Activos
            foreach ($r->activos as $a) {
                $discosStr = collect($a->almacenamiento_discos)->map(fn($d) => ($d['tipo'] ?? '') . ': ' . ($d['capacidad'] ?? '') . 'GB')->implode(' | ');
                $monitoresStr = collect($a->monitores)->map(fn($m) => ($m['marca'] ?? '') . ' ' . ($m['modelo'] ?? '') . ' (' . ($m['pulgadas'] ?? '') . '")')->implode(' | ');
                $softwareStr = implode(', ', $a->software_instalado ?? []);

                $mouseStr = $a->tiene_mouse ? (($a->mouse_marca ?? '') . ' ' . ($a->mouse_modelo ?? '') . ' (' . ($a->mouse_conexion ?? '') . ')') : '';
                $tecladoStr = $a->tiene_teclado ? (($a->teclado_marca ?? '') . ' ' . ($a->teclado_modelo ?? '') . ' (' . ($a->teclado_conexion ?? '') . ')') : '';
                $estabilizadorStr = $a->tiene_estabilizador ? (($a->estabilizador_marca ?? '') . ' ' . ($a->estabilizador_modelo ?? '') . ' (' . ($a->estabilizador_color ?? '') . ')') : '';
                $camaraStr = $a->tiene_camara ? (($a->camara_marca ?? '') . ' ' . ($a->camara_modelo ?? '') . ' (' . ($a->camara_conexion ?? '') . ')') : '';
                $parlantesStr = $a->tiene_parlantes ? (($a->parlantes_marca ?? '') . ' ' . ($a->parlantes_modelo ?? '')) : '';

                $sheetActivos[] = [
                    $a->id,
                    $r->id,
                    $oficinaNombre,
                    $r->responsable_nombre,
                    $a->codigo_inventario,
                    $a->tipo,
                    $a->estado,
                    $a->tipo_red,
                    $a->ip,
                    $a->mac,
                    $a->so_tipo,
                    $a->so_version,
                    $a->marca,
                    $a->modelo,
                    $a->cpu_modelo,
                    $a->cpu_ghz,
                    $a->ram_gb,
                    $a->ram_tipo,
                    $a->placa_video,
                    $a->motherboard,
                    $discosStr,
                    $monitoresStr,
                    $a->tiene_mouse ? 'SÍ' : 'NO',
                    $mouseStr,
                    $a->tiene_teclado ? 'SÍ' : 'NO',
                    $tecladoStr,
                    $a->tiene_estabilizador ? 'SÍ' : 'NO',
                    $estabilizadorStr,
                    $a->tiene_camara ? 'SÍ' : 'NO',
                    $camaraStr,
                    $a->tiene_parlantes ? 'SÍ' : 'NO',
                    $parlantesStr,
                    $softwareStr,
                    $a->usuario_nombre,
                    $a->usuario_apellido,
                    $a->usuario_caracter
                ];
            }

            // Pestaña Servidores
            if ($s = $r->servidor) {
                $discosSrvStr = collect($s->almacenamiento_discos)->map(fn($d) => ($d['tipo'] ?? '') . ': ' . ($d['capacidad'] ?? '') . 'GB')->implode(' | ');
                $sheetServidores[] = [
                    $s->id,
                    $r->id,
                    $oficinaNombre,
                    $r->responsable_nombre,
                    $s->funcion,
                    $s->sistema_operativo,
                    $s->so_version,
                    $s->arquitectura,
                    $s->requerimientos_especiales,
                    $s->tipo_equipamiento,
                    $s->cpu_ghz,
                    $s->ram_gb,
                    $s->ram_tipo,
                    $s->swap_gb,
                    $s->almacenamiento_gb,
                    $discosSrvStr,
                    $s->motor_bd,
                    $s->cantidad_bases,
                    $s->tamano_bases_gb,
                    $s->usuarios_concurrentes,
                    $s->usuarios_totales,
                    $s->estimacion_crecimiento,
                    $s->licencia_tipo,
                    $s->licencia_cantidad,
                    $s->licencia_observaciones,
                    $s->red_lan,
                    $s->wan,
                    $s->internet_dedicado,
                    $s->trafico_estimado_mb
                ];
            }

            // Pestaña Equipos Adicionales
            if ($ea = $r->equiposAdicionales) {
                $otrosStr = collect($ea->otros_dispositivos)->map(fn($o) => ($o['descripcion'] ?? '') . ' (Inv: ' . ($o['id_inventario'] ?? '') . ')')->implode(' | ');
                $sheetAdicionales[] = [
                    $ea->id,
                    $r->id,
                    $oficinaNombre,
                    $r->responsable_nombre,
                    $ea->tiene_proyector ? 'SÍ' : 'NO',
                    $ea->tiene_proyector ? (($ea->proyector_marca ?? '') . ' (' . ($ea->proyector_conexion ?? '') . ')') : '',
                    $ea->proyector_id_inventario,
                    $ea->tiene_led ? 'SÍ' : 'NO',
                    $ea->tiene_led ? (($ea->led_marca ?? '') . ' ' . ($ea->led_pulgadas ?? '') . '" (' . ($ea->led_conexion ?? '') . ')') : '',
                    $ea->led_id_inventario,
                    $ea->tiene_camara ? 'SÍ' : 'NO',
                    $ea->tiene_camara ? (($ea->camara_marca ?? '') . ' (' . ($ea->camara_conexion ?? '') . ')') : '',
                    $ea->camara_id_inventario,
                    $ea->cantidad_camaras_vigilancia,
                    implode(', ', $ea->camaras_vigilancia_ids ?? []),
                    $otrosStr
                ];
            }

            // Pestaña Impresoras
            if ($r->tiene_impresora && is_array($r->impresoras)) {
                foreach ($r->impresoras as $imp) {
                    $sheetImpresoras[] = [
                        $r->id,
                        $oficinaNombre,
                        $r->responsable_nombre,
                        $imp['marca'] ?? '',
                        $imp['modelo'] ?? '',
                        $imp['ip'] ?? '',
                        $imp['conexion'] ?? '',
                        ($imp['escaner'] ?? false) ? 'SÍ' : 'NO',
                        $imp['tipo'] ?? ''
                    ];
                }
            }

            // Pestaña Módems
            if ($r->tiene_modem && is_array($r->modems)) {
                foreach ($r->modems as $mod) {
                    $sheetModems[] = [
                        $r->id,
                        $oficinaNombre,
                        $r->responsable_nombre,
                        $mod['marca'] ?? '',
                        $mod['modelo'] ?? '',
                        $mod['ssid'] ?? '',
                        $mod['password'] ?? '',
                        $mod['ip'] ?? '',
                        $mod['proxy'] ?? '',
                        $mod['usuario'] ?? '',
                        $mod['clave'] ?? '',
                        $mod['mac'] ?? ''
                    ];
                }
            }
        }

        // Crear el archivo Excel usando SimpleXLSXGen
        $xlsx = SimpleXLSXGen::fromArray($sheetRelevamientos, 'Relevamientos');

        if (count($sheetActivos) > 1) {
            $xlsx->addSheet($sheetActivos, 'Activos');
        }
        if (count($sheetServidores) > 1) {
            $xlsx->addSheet($sheetServidores, 'Servidores');
        }
        if (count($sheetAdicionales) > 1) {
            $xlsx->addSheet($sheetAdicionales, 'Eq Adicionales');
        }
        if (count($sheetImpresoras) > 1) {
            $xlsx->addSheet($sheetImpresoras, 'Impresoras');
        }
        if (count($sheetModems) > 1) {
            $xlsx->addSheet($sheetModems, 'Modems');
        }

        $xlsx->saveAs($path);
    }
}
