<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa CITAS_SIHCE_RAW y actualiza CITAS_SIHCE en chunks de 10000';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunk = 5000;
        $this->info('Inicio ' . now());
        DB::connection('sihce')
            ->table('CITAS_SIHCE_RAW')
            //->whereRaw("
              //   [Cod_EESS] = '34312' AND [Nro_ticket] = '0001' AND [Cod_Servicio] = '224200' AND [Numero_de_documento] = '74869498' 
            //")
            ->orderBy('Fecha_de_creacion')
            //->limit(10)
            ->chunk($chunk, function ($registros) {
                foreach ($registros as $value) {
                    DB::connection('sihce')
                        ->table('CITAS_SIHCE')->updateOrInsert(
                            [
                                'Cod_EESS' => $value->Cod_EESS,
                                'Nombre_EESS' => $value->Nombre_EESS,
                                'Nro_ticket' => $value->Nro_ticket,
                                'Cod_Servicio' => $value->Cod_Servicio,
                                'Descripcion_del_servicio' => $value->Descripcion_del_servicio,
                                'Profesional_de_la_Salud' => $value->Profesional_de_la_Salud,
                                'Consultorio' => $value->Consultorio,
                                'turno' => $value->turno,
                                'Fecha_de_cita' => Carbon::parse($value->Fecha_de_cita)->format('d/m/Y'),
                                'Hora_inicial_cita' => Carbon::parse($value->Hora_inicial_cita)->format('H:i:s'),
                                'Hora_final_cita' => Carbon::parse($value->Hora_final_cita)->format('H:i:s'),
                                'Tipo_de_cupo' => $value->Tipo_de_cupo,
                                'Fecha_de_creacion' => Carbon::parse($value->Fecha_de_creacion)->format('d/m/Y'),
                                'Tipo_de_documento' => $value->Tipo_de_documento,
                                'Numero_de_documento' => $value->Numero_de_documento,
                                'Nombres_del_paciente' => $value->Nombres_del_paciente,
                                'Apellido_paterno_del_paciente' => $value->Apellido_paterno_del_paciente,
                                'Apellido_materno_del_paciente' => $value->Apellido_materno_del_paciente,
                                'Celular' => $value->Celular,
                                'Departamento' => $value->Departamento,
                                'Provincia' => $value->Provincia,
                                'Distrito' => $value->Distrito,
                                'Direccion_actual' => $value->Direccion_actual,
                                'Genero' => $value->Genero,
                                'Fecha_Nac' => Carbon::parse($value->Fecha_Nac)->format('d/m/Y'),
                                'Numero_de_historia' => $value->Numero_de_historia,
                                'Archivo_clinico' => $value->Archivo_clinico,
                                'EESS_del_asegurado' => $value->EESS_del_asegurado,
                                'Cod_EESS_del_asegurado' => $value->Cod_EESS_del_asegurado,
                                'Personal_que_cita' => $value->Personal_que_cita,
                            ],
                            [
                                'Estado_de_la_cita' => $value->Estado_de_la_cita,
                                'renaes' => trim(preg_replace('/\s+/', '', $value->renaes)),
                                'Fecha_Reporte' => Carbon::parse($value->Fecha_Reporte)->format('d/m/Y H:i:s'),
                                'Personal_que_modifica' => $value->Personal_que_modifica,
                                'Fecha_de_modificacion' => $value->Fecha_de_modificacion
                                    ? Carbon::parse($value->Fecha_de_modificacion)->format('d/m/Y H:i:s')
                                    : null,
                            ]
                        );
                }
                $this->info('Chunk procesado a las ' . now());
            });

        $this->info('Todos los registros han sido procesados. ' . now());
    }
}