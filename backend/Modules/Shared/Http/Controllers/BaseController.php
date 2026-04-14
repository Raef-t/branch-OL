<?php

namespace Modules\Shared\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * @OA\Info(
 *     title="معهد التعليم - وثائق API",
 *     version="1.0.0",
 *     description="واجهات برمجية لإدارة المعهد"
 * )
 *
 * @OA\Server(
 *     url="http://46.225.180.151/test",
 *     description="Staging Server"
 * )
 *
 * @OA\Server(
 *     url="https://norma910-001-site1.mtempurl.com/",
 *     description="Production Server"
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000/",
 *     description="Local Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="أدخل توكن Bearer",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="sanctum"
 * )
 *
 * @OA\Schema(
 *     schema="InstituteBranchResource",
 *     title="Institute Branch Resource",
 *     description="نموذج بيانات الفرع",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="فرع دمشق"),
 *     @OA\Property(property="code", type="string", example="DM1"),
 *     @OA\Property(property="country_code", type="string", example="+963"),
 *     @OA\Property(property="address", type="string", example="دمشق - شارع بغداد"),
 *     @OA\Property(property="phone", type="string", example="+963912345678"),
 *     @OA\Property(property="email", type="string", example="damascus@institute.com"),
 *     @OA\Property(property="manager_name", type="string", example="أحمد محمد"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     title="Validation Error Response",
 *     @OA\Property(property="message", type="string", example="Validation failed"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(
 *             property="name",
 *             type="array",
 *             @OA\Items(type="string", example="اسم الفرع مطلوب.")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="FamilyResource",
 *     title="Family Resource",
 *     description="نموذج بيانات العائلة",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=123, nullable=true),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=123),
 *         @OA\Property(property="name", type="string", example="خالد أحمد"),
 *         @OA\Property(property="role", type="string", example="family")
 *     ),
 *     @OA\Property(property="students_count", type="integer", example=2, nullable=true),
 *     @OA\Property(property="guardians_count", type="integer", example=2, nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-05T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-05T10:30:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="StudentStatusResource",
 *     title="Student Status Resource",
 *     description="نموذج بيانات حالة الطالب",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="طالب حالي"),
 *     @OA\Property(property="code", type="string", example="PRESENT"),
 *     @OA\Property(property="description", type="string", example="الطالب مسجل ويدرس حاليًا", nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-05T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-05T10:30:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="AcademicRecordResource",
 *     title="Academic Record Resource",
 *     description="نموذج بيانات السجل الأكاديمي",
 *     required={"student_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="student_id", type="integer", example=123),
 *     @OA\Property(property="record_type", type="string", example="bac_passed", enum={"ninth_grade", "bac_failed", "bac_passed", "other"}, nullable=true),
 *     @OA\Property(property="total_score", type="number", format="float", example=85.50, nullable=true),
 *     @OA\Property(property="year", type="integer", example=2024, nullable=true),
 *     @OA\Property(property="description", type="string", example="نجح في امتحان البكالوريا", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-05T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-05T10:30:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="StudentResource",
 *     title="Student Resource",
 *     description="نموذج بيانات الطالب",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="institute_branch_id", type="integer", example=1),
 *     @OA\Property(property="family_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="user_id", type="integer", example=123, nullable=true),
 *     @OA\Property(property="first_name", type="string", example="أحمد"),
 *     @OA\Property(property="last_name", type="string", example="محمد"),
 *     @OA\Property(property="full_name", type="string", example="أحمد محمد"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", example="2008-05-15", nullable=true),
 *     @OA\Property(property="birth_place", type="string", example="دمشق", nullable=true),
 *     @OA\Property(property="profile_photo_url", type="string", format="uri", example="https://example.com/photo.jpg", nullable=true),
 *     @OA\Property(property="id_card_photo_url", type="string", format="uri", example="https://example.com/id.jpg", nullable=true),
 *     @OA\Property(property="branch_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="enrollment_date", type="string", format="date", example="2025-09-01"),
 *     @OA\Property(property="start_attendance_date", type="string", format="date", example="2025-09-15", nullable=true),
 *     @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male", nullable=true),
 *     @OA\Property(property="previous_school_name", type="string", example="مدرسة النجاح", nullable=true),
 *     @OA\Property(property="national_id", type="string", example="123456789", nullable=true),
 *     @OA\Property(property="how_know_institute", type="string", example="من صديق", nullable=true),
 *     @OA\Property(property="bus_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="notes", type="string", example="طالب متميز", nullable=true),
 *     @OA\Property(property="status_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="city_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="qr_code_data", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-05T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-05T10:30:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Subject",
 *     title="Subject Resource",
 *     description="نموذج مادة دراسية",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="رياضيات"),
 *     @OA\Property(property="code", type="string", example="MATH101"),
 *     @OA\Property(property="description", type="string", example="مادة الرياضيات الأساسية"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="GuardianResource",
 *     title="Guardian Resource",
 *     description="نموذج بيانات ولي الأمر",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="family_id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="أحمد"),
 *     @OA\Property(property="last_name", type="string", example="محمد"),
 *     @OA\Property(property="national_id", type="string", example="123456789", nullable=true),
 *     @OA\Property(property="phone", type="string", example="+963912345678"),
 *     @OA\Property(property="is_primary_contact", type="boolean", example=true),
 *     @OA\Property(property="occupation", type="string", example="مهندس", nullable=true),
 *     @OA\Property(property="address", type="string", example="دمشق، المزة", nullable=true),
 *     @OA\Property(property="relationship", type="string", example="father", enum={"father", "mother", "legal_guardian", "other"}),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-05T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-05T10:30:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="RoleResource",
 *     title="Role Resource",
 *     description="نموذج بيانات الدور مع صلاحياته",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="admin"),
 *     @OA\Property(property="guard_name", type="string", example="web"),
 *     @OA\Property(
 *         property="permissions",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/PermissionResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PermissionResource",
 *     title="Permission Resource",
 *     description="نموذج بيانات الصلاحية",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="edit-users"),
 *     @OA\Property(property="guard_name", type="string", example="web")
 * )
 *
 * @OA\Schema(
 *     schema="StoreRoleRequest",
 *     title="Store Role Request",
 *     description="بيانات إنشاء أو تحديث دور",
 *     required={"name", "permissions"},
 *     @OA\Property(property="name", type="string", example="editor"),
 *     @OA\Property(
 *         property="permissions",
 *         type="array",
 *         @OA\Items(type="string", example="view-reports")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UserRolesResource",
 *     title="User Roles Resource",
 *     description="بيانات المستخدم مع أدواره",
 *     @OA\Property(property="id", type="integer", example=123),
 *     @OA\Property(property="name", type="string", example="خالد أحمد"),
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         @OA\Items(type="string", example="family")
 *     )
 * )
 * * @OA\Schema(
 *     schema="AssignRoleRequest",
 *     title="Assign Role Request",
 *     description="بيانات ربط دور بمستخدم",
 *     required={"user_id", "role_name"},
 *     @OA\Property(property="user_id", type="integer", example=123),
 *     @OA\Property(property="role_name", type="string", example="admin")
 * )
 *  * @OA\Schema(
 *     schema="RemoveRoleRequest",
 *     title="Remove Role Request",
 *     description="جسم الطلب لإزالة دور واحد من مستخدم",
 *     type="object",
 *     required={"user_id","role_name"},
 *     @OA\Property(property="user_id",   type="integer", example=123),
 *     @OA\Property(property="role_name", type="string",  example="editor")
 * )
 * @OA\Schema(
 *     schema="BulkRemoveRolesRequest",
 *     title="Bulk Remove Roles Request",
 *     description="جسم الطلب لإزالة عدة أدوار من مستخدم دفعة واحدة",
 *     type="object",
 *     required={"user_id","role_names"},
 *     @OA\Property(property="user_id",    type="integer", example=123),
 *     @OA\Property(
 *         property="role_names",
 *         type="array",
 *         @OA\Items(type="string", example="editor")
 *     )
 * )

 * @OA\Schema(
 *     schema="MessageTemplateStoreRequest",
 *     title="Message Template Store Request",
 *     description="بيانات إنشاء قالب رسالة (القيم المسموحة موضحة صراحة)",
 *     type="object",
 *     required={"name","type","category","body"},
 *
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="اسم قالب الرسالة"
 *     ),
 *
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="نوع الرسالة (القيم المسموحة فقط)",
 *         enum={"sms","in_app","email"}
 *     ),
 *
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="تصنيف الرسالة (القيم المسموحة فقط)",
 *         enum={
 *             "general",
 *             "attendance",
 *             "absence",
 *             "behavior",
 *             "exam",
 *             "financial"
 *         }
 *     ),
 *
 *     @OA\Property(
 *         property="subject",
 *         type="string",
 *         nullable=true,
 *         description="عنوان الرسالة (اختياري)"
 *     ),
 *
 *     @OA\Property(
 *         property="body",
 *         type="string",
 *         description="محتوى الرسالة"
 *     ),
 *
 *     @OA\Property(
 *         property="is_active",
 *         type="boolean",
 *         nullable=true,
 *         description="حالة التفعيل"
 *     )
 * )
 *  * @OA\Schema(
 *     schema="MessageTemplateResource",
 *     title="Message Template",
 *     description="تمثيل قالب الرسالة",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="تنبيه دفعة متأخرة"
 *     ),
 *
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="نوع الرسالة",
 *         enum={"sms","in_app","email"},
 *         example="sms"
 *     ),
 *
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="تصنيف الرسالة",
 *         enum={
 *             "general",
 *             "attendance",
 *             "absence",
 *             "behavior",
 *             "exam",
 *             "financial"
 *         },
 *         example="financial"
 *     ),
 *
 *     @OA\Property(
 *         property="subject",
 *         type="string",
 *         nullable=true,
 *         example="تذكير بالدفعة"
 *     ),
 *
 *     @OA\Property(
 *         property="body",
 *         type="string",
 *         example="مرحبًا {student_name}، يرجى دفع القسط المستحق بتاريخ {due_date}."
 *     ),
 *
 *     @OA\Property(
 *         property="is_active",
 *         type="boolean",
 *         example=true
 *     ),
 *
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-01-01T00:00:00.000000Z"
 *     ),
 *
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-01-01T00:00:00.000000Z"
 *     )
 * )
 * * @OA\Schema(
 *     schema="NotificationResource",
 *     title="Notification Resource",
 *     description="نموذج بيانات الإشعار",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="template_id", type="integer", nullable=true, example=5),
 *     @OA\Property(
 *         property="sender",
 *         type="object",
 *         @OA\Property(property="type", type="string", example="system"),
 *         @OA\Property(property="id", type="integer", nullable=true, example=null),
 *         @OA\Property(property="display_name", type="string", example="النظام"),
 *         @OA\Property(property="is_system", type="boolean", example=true)
 *     ),
 *     @OA\Property(property="title", type="string", example="إغلاق النظام للصيانة"),
 *     @OA\Property(property="body", type="string", example="سيتم إغلاق النظام للصيانة يوم غد"),
 *     @OA\Property(property="type", type="string", enum={"sms","in_app","email","media","all"}, example="in_app"),
 *     @OA\Property(property="target_type", type="string", enum={"student","parent","staff","all"}, example="all"),
 *     @OA\Property(property="target_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="sent_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="status", type="string", enum={"pending","sent","failed","cancelled"}, example="sent"),
 *     @OA\Property(property="is_scheduled", type="boolean", example=false),
 *     @OA\Property(property="is_sent", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-31 10:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-01-31 10:00:00"),
 *     @OA\Property(
 *         property="attachments",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/NotificationAttachmentResource")
 *     ),
 *     @OA\Property(property="attachments_count", type="integer", example=0),
 *     @OA\Property(
 *         property="recipients",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/NotificationRecipientResource")
 *     ),
 *     @OA\Property(property="recipients_count", type="integer", example=0),
 *     @OA\Property(
 *         property="recipients_stats",
 *         type="object",
 *         @OA\Property(property="total", type="integer", example=0),
 *         @OA\Property(property="read", type="integer", example=0),
 *         @OA\Property(property="unread", type="integer", example=0),
 *         @OA\Property(property="delivered", type="integer", example=0)
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="NotificationAttachmentResource",
 *     title="Notification Attachment Resource",
 *     description="نموذج بيانات مرفق الإشعار",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="notification_id", type="integer", example=1),
 *     @OA\Property(property="file_name", type="string", example="document.pdf"),
 *     @OA\Property(property="file_path", type="string", example="notifications/1/2026/01/31/uuid_file.pdf"),
 *     @OA\Property(property="file_url", type="string", example="/storage/notifications/1/2026/01/31/uuid_file.pdf"),
 *     @OA\Property(property="file_size", type="integer", example=204800),
 *     @OA\Property(property="file_size_formatted", type="string", example="200 KB"),
 *     @OA\Property(property="file_type", type="string", example="document"),
 *     @OA\Property(property="mime_type", type="string", example="application/pdf"),
 *     @OA\Property(property="file_extension", type="string", example="pdf"),
 *     @OA\Property(property="title", type="string", nullable=true, example=null),
 *     @OA\Property(property="description", type="string", nullable=true, example=null),
 *     @OA\Property(property="sort_order", type="integer", example=0),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="is_image", type="boolean", example=false),
 *     @OA\Property(property="is_document", type="boolean", example=true),
 *     @OA\Property(property="download_url", type="string", example="http://localhost/storage/notifications/1/2026/01/31/uuid_file.pdf"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-31 10:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-01-31 10:00:00")
 * )
 *
 * @OA\Schema(
 *     schema="NotificationRecipientResource",
 *     title="Notification Recipient Resource",
 *     description="نموذج بيانات مستلم الإشعار",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="notification_id", type="integer", example=1),
 *     @OA\Property(property="recipient_id", type="integer", example=123),
 *     @OA\Property(property="recipient_type", type="string", example="Modules\\Students\\Models\\Student"),
 *     @OA\Property(
 *         property="recipient",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=123),
 *         @OA\Property(property="name", type="string", example="محمد أحمد"),
 *         @OA\Property(property="type", type="string", example="Student")
 *     ),
 *     @OA\Property(property="status", type="string", enum={"pending","delivered","read","failed"}, example="delivered"),
 *     @OA\Property(property="delivered_at", type="string", format="date-time", nullable=true, example="2026-01-31 10:00:00"),
 *     @OA\Property(property="read_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="failed_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="failure_reason", type="string", nullable=true, example=null),
 *     @OA\Property(property="channel", type="string", nullable=true, enum={"sms","in_app","email","push"}, example="in_app"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-31 10:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-01-31 10:00:00")
 * )
 *
 * @OA\Schema(
 *     schema="StoreNotificationRequest",
 *     title="Store Notification Request",
 *     description="بيانات إنشاء إشعار جديد",
 *     required={"title","body","type","status"},
 *     @OA\Property(property="sender_type", type="string", nullable=true, enum={"system","user","admin","employee","teacher"}, example="system"),
 *     @OA\Property(property="sender_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="sender_display_name", type="string", nullable=true, maxLength=255, example="النظام"),
 *     @OA\Property(property="template_id", type="integer", nullable=true, example=5),
 *     @OA\Property(property="title", type="string", maxLength=255, example="إشعار هام"),
 *     @OA\Property(property="body", type="string", example="محتوى الإشعار"),
 *     @OA\Property(property="type", type="string", enum={"sms","in_app","email","media","all"}, example="in_app"),
 *     @OA\Property(property="target_type", type="string", nullable=true, enum={"student","parent","staff","all"}, example="all"),
 *     @OA\Property(property="target_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="sent_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="status", type="string", enum={"pending","sent","failed","cancelled"}, example="pending")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateNotificationRequest",
 *     title="Update Notification Request",
 *     description="بيانات تحديث إشعار",
 *     @OA\Property(property="sender_type", type="string", nullable=true, enum={"system","user","admin","employee","teacher"}, example="system"),
 *     @OA\Property(property="sender_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="sender_display_name", type="string", nullable=true, maxLength=255, example="النظام"),
 *     @OA\Property(property="template_id", type="integer", nullable=true, example=5),
 *     @OA\Property(property="title", type="string", maxLength=255, example="إشعار محدث"),
 *     @OA\Property(property="body", type="string", example="محتوى الإشعار المحدث"),
 *     @OA\Property(property="type", type="string", enum={"sms","in_app","email","media","all"}, example="in_app"),
 *     @OA\Property(property="target_type", type="string", nullable=true, enum={"student","parent","staff","all"}, example="all"),
 *     @OA\Property(property="target_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="sent_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="status", type="string", enum={"pending","sent","failed","cancelled"}, example="sent")
 * )
 *
 * @OA\Schema(
 *     schema="SendNotificationRequest",
 *     title="Send Notification Request",
 *     description="بيانات إرسال إشعار للمستلمين",
 *     required={"title","body","type","recipients"},
 *     @OA\Property(property="sender_type", type="string", nullable=true, enum={"system","user","admin","employee","teacher"}, example="system"),
 *     @OA\Property(property="sender_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="sender_display_name", type="string", nullable=true, maxLength=255, example="النظام"),
 *     @OA\Property(property="title", type="string", maxLength=255, example="إشعار جديد"),
 *     @OA\Property(property="body", type="string", example="محتوى الإشعار الجديد"),
 *     @OA\Property(property="type", type="string", enum={"sms","in_app","email","media","all"}, example="in_app"),
 *     @OA\Property(
 *         property="recipients",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"id","model_type"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="model_type", type="string", enum={"student","parent","staff"}, example="student")
 *         )
 *     ),
 *     @OA\Property(
 *         property="attachments",
 *         type="array",
 *         @OA\Items(type="string", format="binary")
 *     )
 * )
 *  * @OA\Schema(
 *     schema="NotificationItemResource",
 *     title="Notification Item Resource",
 *     description="تمثيل عنصر إشعار فردي مع تفاصيل المستلم",
 *     @OA\Property(property="id", type="integer", example=31, description="معرف الإشعار"),
 *     @OA\Property(property="title", type="string", example="اجتماع مهم", description="عنوان الإشعار"),
 *     @OA\Property(property="body", type="string", example="يرجى الحضور لاجتماع طارئ الساعة 3 عصراً", description="محتوى الإشعار"),
 *     @OA\Property(
 *         property="sender",
 *         type="object",
 *         description="معلومات المرسل",
 *         @OA\Property(property="id", type="integer", example=null, nullable=true),
 *         @OA\Property(property="type", type="string", example="admin", enum={"admin","system","user"})
 *     ),
 *     @OA\Property(
 *         property="template",
 *         type="object",
 *         nullable=true,
 *         description="القالب المستخدم (إن وجد)",
 *         @OA\Property(property="id", type="integer", example=5),
 *         @OA\Property(property="name", type="string", example="قالب تنبيه هام")
 *     ),
 *     @OA\Property(
 *         property="target_snapshot",
 *         type="object",
 *         description="بيانات المستهدفين وقت الإنشاء",
 *         @OA\Property(property="type", type="string", example="custom", enum={"all","branch","custom"}),
 *         @OA\Property(property="user_ids", type="array", @OA\Items(type="integer", example=1))
 *     ),
 *     @OA\Property(
 *         property="attachments",
 *         type="array",
 *         description="المرفقات المرتبطة بالإشعار",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="file_name", type="string", example="محضر_الاجتماع.pdf"),
 *             @OA\Property(property="file_path", type="string", example="notifications/31/document.pdf"),
 *             @OA\Property(property="url", type="string", example="http://127.0.0.1:8000/storage/notifications/31/document.pdf", description="رابط مباشر للتنزيل"),
 *             @OA\Property(property="mime_type", type="string", example="application/pdf"),
 *             @OA\Property(property="size", type="integer", example=204800, description="الحجم بالبايت"),
 *             @OA\Property(property="size_formatted", type="string", example="200 KB", description="الحجم بتنسيق مقروء")
 *         )
 *     ),
 *     @OA\Property(property="recipients_count", type="integer", example=5, description="إجمالي عدد المستلمين لهذا الإشعار"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-04 15:30:00", description="تاريخ الإنشاء"),
 *     @OA\Property(property="created_at_human", type="string", example="منذ 5 دقائق", description="تاريخ الإنشاء بتنسيق بشري"),
 *     @OA\Property(property="read_at", type="string", format="date-time", example=null, nullable=true, description="تاريخ القراءة (null إذا لم يُقرأ)"),
 *     @OA\Property(property="delivered_at", type="string", format="date-time", example="2026-02-04 15:30:05", nullable=true, description="تاريخ التسليم عبر الفايربيز"),
 *     @OA\Property(property="is_read", type="boolean", example=false, description="حالة القراءة")
 * )
 *  * @OA\Schema(
 *     schema="UnauthorizedResponse",
 *     title="Unauthorized Response",
 *     description="استجابة عدم التفويض",
 *     @OA\Property(property="status", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Unauthorized")
 * )
 * /**
 * @OA\Schema(
 *     schema="AdminNotificationResource",
 *     title="Admin Notification Resource",
 *     description="نموذج بيانات الإشعار للإدارة",
 *     @OA\Property(property="id", type="integer", example=7),
 *     @OA\Property(property="title", type="string", example="تنبيه هام"),
 *     @OA\Property(property="body", type="string", example="المحتوى..."),
 *     @OA\Property(property="sender", type="object",
 *         @OA\Property(property="type", type="string"),
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="display_name", type="string")
 *     ),
 *     @OA\Property(property="distribution", type="object",
 *         @OA\Property(property="total_recipients", type="integer"),
 *         @OA\Property(property="read_count", type="integer"),
 *         @OA\Property(property="delivered_count", type="integer"),
 *         @OA\Property(property="read_percentage", type="number"),
 *         @OA\Property(property="delivered_percentage", type="number")
 *     ),
 *     @OA\Property(property="attachments", type="object",
 *         @OA\Property(property="count", type="integer")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at_human", type="string"),
 *     @OA\Property(property="target_snapshot", type="object"),
 *     @OA\Property(property="status", type="string")
 * )
 */

class BaseController extends Controller
{
    // هذا الملف لا يحتوي على أي كود — فقط تعليقات Swagger.
}
