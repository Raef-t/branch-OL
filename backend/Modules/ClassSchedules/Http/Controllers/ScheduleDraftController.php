<?php

namespace Modules\ClassSchedules\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ClassSchedules\Models\ClassScheduleDraft;
use Illuminate\Support\Facades\DB;
use Modules\ClassSchedules\Models\ClassSchedule; // تأكد من وجود الموديل النهائي
use Modules\Batches\Models\Batch;

class ScheduleDraftController extends Controller
{
    /**
     * عرض قائمة بكل "مجموعات" المسودات المولدة
     */
    public function index()
    {
        try {
            $draftGroups = ClassScheduleDraft::select('draft_group_id', DB::raw('max(created_at) as created_at'), DB::raw('count(*) as total_lessons'))
                ->groupBy('draft_group_id')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $draftGroups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في السيرفر: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تفاصيل مسودة معينة (جدول كامل لشعبة)
     */
    public function show($draftGroupId)
    {
        $drafts = ClassScheduleDraft::where('draft_group_id', $draftGroupId)
            ->with([
                'batchSubject.subject', 
                'batchSubject.batch',
                'batchSubject.instructorSubject.instructor',
                'classRoom'
            ])
            ->get();

        if ($drafts->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'المسودة غير موجودة'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $drafts
        ]);
    }

    /**
     * اعتماد المسودة: نقل البيانات من draft_group إلى الجدول الحقيقي
     */
    public function publish(Request $request, $draftGroupId)
    {
        // 1. جلب المسودات الصالحة (غير المتعارضة)
        $drafts = ClassScheduleDraft::where('draft_group_id', $draftGroupId)
            ->where('is_conflict', false)
            ->with('batchSubject.batch')
            ->get();
        
        if ($drafts->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'لا توجد حصص صالحة للاعتماد في هذه المسودة'], 400);
        }

        // 2. خارطة أوقات الحصص (Matching ScheduleGenController)
        $timeMap = [
            1 => ['start' => '08:00', 'end' => '08:45'],
            2 => ['start' => '08:50', 'end' => '09:35'],
            3 => ['start' => '09:40', 'end' => '10:25'],
            4 => ['start' => '10:30', 'end' => '11:15'],
            5 => ['start' => '11:20', 'end' => '12:05'],
        ];

        // 3. تحديد الشعب المتأثرة لمسح جداولها الافتراضية
        $batchIds = $drafts->pluck('batchSubject.batch_id')->unique()->filter();

        DB::beginTransaction();
        try {
            // أ. مسح الجداول الافتراضية الحالية لهذه الشعب فقط (حماية الجداول الاستثنائية)
            ClassSchedule::whereIn('batch_subject_id', function($query) use ($batchIds) {
                $query->select('id')->from('batch_subjects')->whereIn('batch_id', $batchIds);
            })
            ->where('is_default', true)
            ->delete();

            // ب. إدراج الحصص الجديدة
            foreach ($drafts as $draft) {
                $times = $timeMap[$draft->period_number] ?? ['start' => '00:00', 'end' => '00:00'];
                
                // تحصين: إذا كانت القاعة في المسودة هي نفسها الـ Batch ID (خطأ قديم)، نأخذ قاعة الشعبة
                $finalRoomId = $draft->class_room_id;
                $batchRoomId = $draft->batchSubject?->batch?->class_room_id;
                
                // إذا كان ID القاعة يساوي ID الشعبة، فهذا غالباً خطأ في التوليد القديم
                if ($finalRoomId == $draft->batchSubject?->batch_id && $batchRoomId) {
                    $finalRoomId = $batchRoomId;
                }

                ClassSchedule::create([
                    'batch_subject_id' => $draft->batch_subject_id,
                    'day_of_week'      => $draft->day_of_week,
                    'period_number'    => $draft->period_number,
                    'start_time'       => $times['start'],
                    'end_time'         => $times['end'],
                    'class_room_id'    => $finalRoomId,
                    'is_default'       => true,
                    'is_active'        => true,
                    'description'      => 'تم التوليد آلياً عبر المعالج الذكي - مجموعة: ' . $draftGroupId
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'تم اعتماد الجدول بنجاح وتحديث الجداول الافتراضية لـ ' . count($batchIds) . ' شعبة.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'حدث خطأ أثناء الاعتماد: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($draftGroupId)
    {
        try {
            $deletedCount = ClassScheduleDraft::where('draft_group_id', $draftGroupId)->delete();
            
            if ($deletedCount === 0) {
                return response()->json(['success' => false, 'message' => 'المسودة غير موجودة'], 404);
            }

            return response()->json(['success' => true, 'message' => 'تم حذف المسودة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()], 500);
        }
    }

    /**
     * حذف مجموعة من المسودات دفعة واحدة
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد مسودات للحذف'], 400);
        }

        try {
            ClassScheduleDraft::whereIn('draft_group_id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'تم حذف المسودات المختارة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()], 500);
        }
    }
    /**
     * تحديث يدوي لمواقع الحصص في المسودة (دراغ أند دروب)
     */
    public function sync(Request $request, $draftGroupId)
    {
        $payload = $request->input('updates'); // [{id, day, period}, ...]

        if (!is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'بيانات غير صالحة'], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($payload as $update) {
                ClassScheduleDraft::where('id', $update['id'])
                    ->where('draft_group_id', $draftGroupId)
                    ->update([
                        'day_of_week'   => $update['day'],
                        'period_number' => $update['period'],
                        'is_conflict'   => false, // نفترض أن المستخدم يحل التعارض يدوياً
                        'conflict_message' => null
                    ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التعديلات اليدوية بنجاح.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }
}
