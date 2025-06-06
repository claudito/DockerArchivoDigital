<?php

namespace App\Http\Controllers;

use App\Models\ADIG\ADIG_ArchivoDigital;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DigitalFileController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: []),
        ];
    }

    function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'archivo' => 'required|mimes:pdf,xlsx,xls,doc,docx,jpg,jpeg,png,gif,json|max:15360',
                'folder' => 'required',
            ]);

            if ($validator->fails()) {
                DB::commit();
                return response()->json([
                    'error' => 1,
                    'message' => 'Error de validación',
                    'data' => $validator->errors()
                ], 422);
            }

            $user = auth('api')->user();

            //Regigtro de archivo en Bucket
            $folder = $request->folder;
            $file = $request->file('archivo');
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
            $fileName = md5($name . time()) . '.' . $extension;

            //Path
            $path = Storage::disk('minio')->putFileAs(
                $folder,
                $file,
                $fileName
            );

            //Registro
            $archivoDigital = ADIG_ArchivoDigital::create([
                'NombreOriginal' => $name,
                'TipoContenido' => $extension,
                'TamanoBytes' =>  $fileSize,
                'RutaAlmacenamiento' => $folder . '/' . $fileName,
                'UsuarioCreacion' => $user->name,
                'FechaCreacion' => Carbon::now()
            ]);

            do {
                $hash = Str::random(30);
            } while (ADIG_ArchivoDigital::where('HashArchivo', $hash)->exists());

            $archivoDigital->update([
                'HashArchivo' => $hash
            ]);

            $url = Storage::disk('minio')->temporaryUrl(
                $archivoDigital->RutaAlmacenamiento,
                now()->addMinutes((int)env('TIME_URL'))
            );


            DB::commit();

            return response()->json([
                'error' => 0,
                'message' => 'Registro Creado Correctamente',
                'data' => [
                    'path' => $path,
                    'url' => $url,
                    'name' => $name,
                    'hash' => $archivoDigital->HashArchivo
                ]

            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    function tracking(Request $request)
    {
        try {
            DB::beginTransaction();

            if (!Storage::disk('minio')->exists($request->path)) {
                DB::commit();
                return response()->json([
                    'error' => 1,
                    'message' => 'Archivo no Encontrado',
                    'data' => []

                ], 401);
            }

            $url = Storage::disk('minio')->temporaryUrl(
                $request->path,
                now()->addMinutes((int)env('TIME_URL'))
            );

            return response()->json([
                'error' => 0,
                'message' => 'Archivo Digital Diris LE',
                'data' => [
                    'path' => $request->path,
                    'url' => $url,
                    'time' => (int)env('TIME_URL')
                ]

            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getCode()], 401);
        }
    }

    //funcion eliminar lógico
    function updateEstado(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = auth('api')->user();

            $path = $request->input('path');
            $nuevoEstado = $request->input('estado');

            // Validar que el estado sea 1 o 0
            if (!in_array($nuevoEstado, [0, 1], true)) {
                DB::commit();
                return response()->json(['error' => 'El estado solo puede ser 0 o 1'], 400);
            }

            // Actualización directa con Eloquent
            $affectedRows = ADIG_ArchivoDigital::where('RutaAlmacenamiento', $path)
                ->update([
                    'Estado' => $nuevoEstado,
                    'UsuarioModificacion' => $user->name,
                    'FechaModificacion' => now()
                ]);

            if ($affectedRows === 0) {
                DB::commit();
                return response()->json(['error' => 'Archivo no encontrado'], 404);
            }

            DB::commit();

            return response()->json([
                'error' => 0,
                'message' => 'Estado actualizado correctamente',
                'data' => [
                    'path' => $path,
                    'url' => '',
                    'time' => null
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
    function copyFilesNetwork(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = auth('api')->user();

            $validator = Validator::make($request->all(), [
                'ruta_local' => ['required', 'regex:/^[a-zA-Z0-9\/_\.\-]+$/'],
                'ruta_s3' => ['required', 'regex:/^[a-zA-Z0-9\/_\.\-]+$/'],
            ]);

            if ($validator->fails()) {
                DB::commit();
                return response()->json([
                    'error' => 1,
                    'message' => 'Error de validación',
                    'data' => $validator->errors()
                ], 422);
            }

            $rutaLocal = $request->ruta_local;
            $folderS3 = Storage::disk('minio')->files($request->ruta_s3);

            if (!is_dir($rutaLocal)) {
                DB::commit();
                return response()->json([
                    'error' => 0,
                    'message' => 'Ruta Remota No Accesible',
                    'data' => []
                ], 500);
            }

            $archivos = [];
            foreach ($folderS3 as $file) {
                $stream = Storage::disk('minio')->readStream($file);
                if (!$stream) {
                    throw new \Exception("No se pudo leer el archivo desde el origen: $file");
                }

                $localFilePath = $rutaLocal . '/' . basename($file);
                $localStream = fopen($localFilePath, 'w');

                if (!$localStream) {
                    fclose($stream);
                    throw new \Exception("No se pudo abrir el archivo local para escritura: $localFilePath");
                }

                stream_copy_to_stream($stream, $localStream);

                fclose($stream);
                fclose($localStream);

                $archivos[] = pathinfo($file, PATHINFO_BASENAME);
            }

            DB::commit();

            return response()->json([
                'error' => 0,
                'message' => 'Archivos Copiados Correctamente',
                'data' => [
                    'archivos' => $archivos
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function sihceDownloadJson(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = auth('api')->user();

            $validator = Validator::make($request->all(), [
                'ruta_s3' => ['required', 'regex:/^[a-zA-Z0-9\/_\.\-]+$/'],
            ]);

            if ($validator->fails()) {
                DB::commit();
                return response()->json([
                    'error' => 1,
                    'message' => 'Error de validación',
                    'data' => $validator->errors()
                ], 422);
            }

            $validateS3 = Storage::disk('minio')->exists($request->ruta_s3);

            if (!$validateS3) {
                DB::commit();
                return response()->json([
                    'error' => 1,
                    'message' => 'Ruta Remota No Accesible',
                    'data' => []
                ], 500);
            }

            $stream = Storage::disk('minio')->readStream($request->ruta_s3);

            if ($stream === false) {
                DB::commit();
                return response()->json([
                    'error' => 1,
                    'message' => 'No se pudo abrir el archivo como stream.',
                    'data' => []
                ], 500);
            }

            $contents = stream_get_contents($stream);
            fclose($stream);

            $json = json_decode($contents, true);
            $data = $json['data'] ?? null;
            if ($data === null) {
                DB::commit();
                return response()->json([
                    'error' => 1,
                    'message' => 'No se encontró la clave "data" en el JSON',
                    'data' => []
                ], 422);
            }

            //dd( $json);
            foreach ($data as $key => $value) {

                if ($value[25] !== 'NaT') {
                    DB::connection('sihce')->table('CITAS_SIHCE_TEMP')
                        ->updateOrInsert([
                            'Cod_EESS' => $value[0],
                            'Nombre_EESS' => $value[1],
                            'Nro_ticket' => $value[2],
                            'Cod_Servicio' => $value[3],
                            'Numero_de_documento' => $value[15],
                            'turno' => $value[7],
                            'Estado_de_la_cita' => $value[12],
                            'Personal_que_cita' => $value[30],
                            'Personal_que_modifica' => $value[31],
                            'Fecha_de_modificacion' => Carbon::createFromFormat('d/m/Y h:i A', $value[32])->format('d/m/Y H:i:s'),
                            'Fecha_Reporte' => Carbon::createFromFormat('d/m/Y h:i A', $value[32])->format('d/m/Y H:i:s'),
                            'Fecha_de_creacion' =>  Carbon::createFromFormat('d/m/Y', trim($value[13]))->format('d/m/Y'),
                            'Fecha_de_cita' => Carbon::parse($value[8])->format('d/m/Y'),
                            'Hora_inicial_cita' => Carbon::parse($value[9])->format('H:i:s'),
                            'Hora_final_cita' => Carbon::parse($value[10])->format('H:i:s'),
                        ], [
                            'Descripcion_del_servicio' => $value[4],
                            'Profesional_de_la_Salud' => $value[5],
                            'Consultorio' => $value[6],
                            'Tipo_de_cupo' => $value[11],
                            'Tipo_de_documento' => $value[14],
                            'Nombres_del_paciente' => $value[16],
                            'Apellido_paterno_del_paciente' => $value[17],
                            'Apellido_materno_del_paciente' => $value[18],
                            'Celular' => $value[19],
                            'Departamento' => $value[20],
                            'Provincia' => $value[21],
                            'Distrito' => $value[22],
                            'Direccion_actual' => $value[23],
                            'Genero' => $value[24],
                            'Fecha_Nac' =>  Carbon::parse($value[25])->format('d/m/Y'),
                            'Numero_de_historia' => $value[26],
                            'Archivo_clinico' => $value[27],
                            'EESS_del_asegurado' => $value[28],
                            'Cod_EESS_del_asegurado' => $value[29],
                            'renaes' => $value[0]
                        ]);
                }
            }

            DB::commit();

            return response()->json([
                'error' => 0,
                'message' => 'Información procesada correctamente',
                'data' => [
                    'archivos' => null
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
