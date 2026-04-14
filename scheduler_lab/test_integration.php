<?php
// ملف اختبار لتهيئة بيئة الدمج ومعرفة كيف يقرأ PHP مخرجات البايثون (JSON)

// مسارات الملفات (قمنا بتحديدها بشكل ديناميكي لتناسب جهازك)
$python_exec = __DIR__ . "/venv/Scripts/python.exe"; // في نظام ويندوز
$api_script  = escapeshellarg(__DIR__ . "/scheduler_api.py");
$input_json  = escapeshellarg(__DIR__ . "/data/input.json");

// أمر التشغيل
$command = "$python_exec $api_script --input $input_json 2> php_error_log.txt";

echo "====================================\n";
echo "⏳ جاري استدعاء بايثون من PHP...\n";
echo "الرجاء الانتظار، قد يستغرق الأمر دقيقة\n";
echo "====================================\n";

// تنفيذ الأمر وجمع المخرجات
$startTime = microtime(true);
$output = shell_exec($command);
$executionTime = round(microtime(true) - $startTime, 2);

// محاولة فك التشفير
$result = json_decode($output, true);

if ($result && isset($result['success']) && $result['success'] === true) {
    echo "✅ نجح الاتصال! (تم الحل خلال $executionTime ثانية)\n";
    echo "🎓 الحصص المجدولة بنجاح: " . count($result['data']['schedule']) . " حصة.\n";
    echo "⚠️ الحصص المعلقة التي فشل بجدولتها: " . count($result['data']['unassigned']) . " حصة.\n\n";
    
    echo "👇 مثال على كيفية تعرف PHP على أول حصة مستلمة (Array):\n";
    print_r($result['data']['schedule'][0]);
    echo "\nهذه المصفوفة جاهزة لعمل Insert داخل قواعد بيانات Laravel 😎\n";
} else {
    echo "❌ فشل الاتصال أو حدث خطأ. المخرجات المستلمة:\n";
    var_dump($output);
    
    echo "\nملاحظة: راجع ملف php_error_log.txt لمعرفة إن كان بايثون طبع أخطاء جانبية.\n";
}
?>
