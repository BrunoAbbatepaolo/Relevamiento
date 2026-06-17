<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExcelExportService;

class ExportDbToExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:all-xlsx {path? : El path o nombre del archivo donde se guardará el Excel}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Exporta toda la base de datos de relevamientos a un único archivo Excel con múltiples pestañas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando exportación de base de datos a Excel...');

        $path = $this->argument('path');
        if (!$path) {
            $fileName = 'relevamiento_completo_' . date('Ymd_His') . '.xlsx';
            $path = storage_path('app/public/' . $fileName);
        }

        ExcelExportService::exportTo($path);

        $this->info("¡Exportación completada con éxito!");
        $this->info("Archivo guardado en: {$path}");
    }
}
