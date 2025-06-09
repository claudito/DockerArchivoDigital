<?php

namespace App\Http\Controllers;

use App\Models\DigitalFile;
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
            $digitalFile = DigitalFile::create([
                'name' => $name,
                'content_type' => $extension,
                'size_bytes' =>  $fileSize,
                'storage_path' => $folder . '/' . $fileName,
                'user_id' => $user->id
            ]);

            do {
                $hash = Str::random(30);
            } while (DigitalFile::where('hash', $hash)->exists());

            $digitalFile->update([
                'hash' => $hash
            ]);

            $url = Storage::disk('minio')->temporaryUrl(
                $digitalFile->storage_path,
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
                    'hash' => $digitalFile->hash
                ]

            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    function createTemp(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                //'archivo' => 'required|mimes:pdf,xlsx,xls,doc,docx,jpg,jpeg,png,gif,json|max:15360',
                'archivo' => 'required|file|max:15360',
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
            $path = Storage::disk('minio_temp')->putFileAs(
                $folder,
                $file,
                $fileName
            );

            //Registro
            $digitalFile = DigitalFile::create([
                'name' => $name,
                'content_type' => $extension,
                'size_bytes' =>  $fileSize,
                'storage_path' => $folder . '/' . $fileName,
                'user_id' => $user->id
            ]);

            do {
                $hash = Str::random(30);
            } while (DigitalFile::where('hash', $hash)->exists());

            $digitalFile->update([
                'hash' => $hash
            ]);

            $url = Storage::disk('minio_temp')->temporaryUrl(
                $digitalFile->storage_path,
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
                    'hash' => $digitalFile->hash
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
}
