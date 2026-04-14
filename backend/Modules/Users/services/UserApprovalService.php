<?php
namespace Modules\Users\Services;

use App\Models\User as ModelsUser;
use Modules\Users\Models\User;

class UserApprovalService
{
    public function approve($id)
    {
        $user = User::find($id);

        if (!$user) {
            throw new \DomainException('المستخدم غير موجود.');
        }

        if ($user->is_approved) {
            throw new \DomainException('تمت الموافقة على هذا المستخدم مسبقاً.');
        }

        $user->is_approved = true;
        $user->save();

        // ✅ إذا كان المستخدم عائلة، قم بتفعيل جميع الطلاب التابعين لها
        if ($user->role === 'family' && $user->family) {
            $studentUserIds = $user->family->students()->pluck('user_id')->filter();
            if ($studentUserIds->isNotEmpty()) {
                User::whereIn('id', $studentUserIds)->update(['is_approved' => true]);
            }
        }

        return $user;
    }
}
