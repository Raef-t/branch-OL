<?php

namespace Modules\Permissions\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Permissions\mode\Permission;





/**
 * الكونترولر الأساسي لإدارة الصلاحيات (Permissions)
 * مهيأ كـ Resource Controller مع دعم للـ JSON وواجهات العرض.
 */
class PermissionsController extends Controller
{
    public function __construct()
    {
        
    }

    /**
     * عرض قائمة الصلاحيات
     */
    public function index(Request $request)
    {
        

    }

    /**
     * صفحة إنشاء صلاحية جديدة
     */
    public function create()
    {
        return view('permissions::create');
    }

    /**
     * حفظ صلاحية جديدة
     */
    public function store(Request $request)
    {
        
    }

    /**
     * عرض صلاحية مفردة
     */
    public function show($id)
    {
        
    }

    /**
     * صفحة تعديل صلاحية
     */
    public function edit($id)
    {
    }

    /**
     * تحديث صلاحية
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * حذف صلاحية
     */
    public function destroy(Request $request, $id)
    {
    }
}