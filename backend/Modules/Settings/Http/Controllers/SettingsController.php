<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Settings\Models\Setting;
use Modules\Shared\Traits\SuccessResponseTrait;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/settings",
     *     summary="جلب إعدادات النظام",
     *     description="يعيد حالة النظام الحالية (مفعل/معطل) مع الرسالة المخصصة إن وجدت.",
     *     tags={"Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الإعدادات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب إعدادات النظام بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="is_system_enabled", type="boolean", example=true),
     *                 @OA\Property(property="maintenance_message", type="string", example="النظام تحت الصيانة حالياً.")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $settings = Setting::first();

        return $this->successResponse(
            $settings,
            'تم جلب إعدادات النظام بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/settings",
     *     summary="تحديث إعدادات النظام",
     *     description="يُمكن للمدير تعطيل النظام أو تشغيله وإضافة رسالة تظهر أثناء التعطيل.",
     *     tags={"Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="is_system_enabled", type="boolean", example=false),
     *             @OA\Property(property="maintenance_message", type="string", example="النظام متوقف مؤقتاً من قبل الإدارة")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث الإعدادات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث إعدادات النظام بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="is_system_enabled", type="boolean", example=false),
     *                 @OA\Property(property="maintenance_message", type="string", example="النظام متوقف من قبل الإدارة")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="بيانات غير صحيحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل التحقق من البيانات"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        $request->validate([
            'is_system_enabled' => 'required|boolean',
            'maintenance_message' => 'nullable|string|max:500'
        ]);

        $settings = Setting::first();

        if (!$settings) {
            $settings = Setting::create($request->only([
                'is_system_enabled',
                'maintenance_message'
            ]));
        } else {
            $settings->update($request->only([
                'is_system_enabled',
                'maintenance_message'
            ]));
        }

        return $this->successResponse(
            $settings,
            'تم تحديث إعدادات النظام بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/settings/backup",
     *     summary="نسخة احتياطية من قاعدة البيانات",
     *     description="ينتج ويحمل نسخة احتياطية من قاعدة البيانات بتنسيق sql.",
     *     tags={"Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم تحميل النسخة الاحتياطية بنجاح",
     *         @OA\MediaType(mediaType="application/octet-stream")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطأ في السيرفر الداخلي"
     *     )
     * )
     */
    public function backup()
    {
        $dbName = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        
        $filename = "backup-" . date('Y-m-d-H-i-s') . ".sql";
        $directory = storage_path('app/backups');
        $path = $directory . '/' . $filename;
        
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $mysqldumpPath = 'mysqldump';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if (File::exists('c:/xampp/mysql/bin/mysqldump.exe')) {
                $mysqldumpPath = 'c:\xampp\mysql\bin\mysqldump.exe';
            }
        }
        
        // Determine the correct SSL flag based on mysqldump version (MySQL vs MariaDB)
        $sslFlag = '--ssl-mode=DISABLED';
        if (strpos(shell_exec("{$mysqldumpPath} --version"), 'MariaDB') !== false) {
            $sslFlag = '--skip-ssl';
        }
        
        $command = "{$mysqldumpPath} {$sslFlag} --user=\"{$user}\" --password=\"{$password}\" --host=\"{$host}\" \"{$dbName}\" --result-file=\"{$path}\" 2>&1";
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json([
                'status' => false,
                'message' => 'فشل إنتاج نسخة احتياطية من قاعدة البيانات',
                'details' => $output
            ], 500);
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }

    /**
     * @OA\Post(
     *     path="/api/settings/restore",
     *     summary="استعادة قاعدة البيانات",
     *     description="يرفع ملف .sql ويستبدل قاعدة البيانات الحالية به. تحذير: هذه العملية ستمسح كافة البيانات الحالية.",
     *     tags={"Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="database_file",
     *                     type="string",
     *                     format="binary",
     *                     description="ملف قاعدة البيانات بتنسيق .sql"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم استعادة قاعدة البيانات بنجاح"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="فشل في عملية الاستعادة"
     *     )
     * )
     */
    public function restore(Request $request)
    {
        $request->validate([
            'database_file' => 'required|file' // Add extension validation if needed
        ]);

        $file = $request->file('database_file');
        
        if ($file->getClientOriginalExtension() !== 'sql') {
            return response()->json([
                'status' => false,
                'message' => 'يرجى رفع ملف بتنسيق .sql فقط'
            ], 422);
        }

        $dbName = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $tempPath = $file->storeAs('temp', 'restore-' . time() . '.sql');
        $path = storage_path('app/' . $tempPath);

        $mysqlPath = 'mysql';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if (File::exists('c:/xampp/mysql/bin/mysql.exe')) {
                $mysqlPath = 'c:\xampp\mysql\bin\mysql.exe';
            }
        }

        // Use source command to avoid shell redirection issues in some environments
        // Note: We use shell_exec or similar to run the command.
        // We need to pass the password carefully.
        $command = "{$mysqlPath} --user=\"{$user}\" --password=\"{$password}\" --host=\"{$host}\" \"{$dbName}\" < \"{$path}\" 2>&1";
        
        // On Windows cmd, the < operator works.
        exec($command, $output, $returnVar);

        // Delete temp file
        File::delete($path);

        if ($returnVar !== 0) {
            return response()->json([
                'status' => false,
                'message' => 'فشل في استعادة قاعدة البيانات',
                'details' => $output
            ], 500);
        }

        return $this->successResponse(null, 'تم استعادة قاعدة البيانات بنجاح', 200);
    }
}

