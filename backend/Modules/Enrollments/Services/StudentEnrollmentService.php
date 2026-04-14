<?php
    namespace Modules\Enrollments\Services;

    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Modules\Families\Models\Family;
    use Modules\Guardians\Models\Guardian;
    use Modules\Students\Models\Student;
    use Modules\ContactDetails\Models\ContactDetail;

    class StudentEnrollmentService
    {
        protected FileUploadService $fileService;

        public function __construct(FileUploadService $fileService)
        {
            $this->fileService = $fileService;
        }

        // ============================================================
        // ===== تطبيع الأسماء العربية لتحسين دقة المطابقة ===========
        // ============================================================

        /**
         * تطبيع الأسم العربي قبل حساب الهاش:
         * - توحيد الألفات: (أ، إ، آ) → (ا)
         * - إزالة التشكيل
         * - إزالة المسافات الزائدة
         *
         * ⚠️ هذه الدالة للبحث فقط — الأسماء تُحفظ في قاعدة البيانات كما هي
         */
        private function normalizeArabicName(string $name): string
        {
            $name = trim($name);
            // توحيد ألفات الوصل والقطع والمد → ألف عادية
            $name = str_replace(['أ', 'إ', 'آ'], 'ا', $name);
            // توحيد التاء المربوطة والهاء في آخر الكلمة
            $name = str_replace(['ة'], 'ه', $name);
            // توحيد الياء والألف المقصورة
            $name = str_replace(['ى'], 'ي', $name);
            // إزالة التشكيل (حركات الضم والفتح والكسر وغيرها)
            $name = preg_replace('/[\x{064B}-\x{065F}]/u', '', $name);
            // توحيد المسافات المتعددة
            $name = preg_replace('/\s+/', ' ', $name);
            return $name;
        }

        // ============================================================
        // ===== الواجهات العامة ======================================
        // ============================================================

        /**
         * التحقق من وجود عائلات مطابقة وإرجاعها كمجموعة.
         * تبحث أولاً بالأسماء (هاش)، وإذا لم تجد، تبحث بأرقام الهاتف.
         *
         * @return Collection<Family> — قائمة العائلات المطابقة
         */
        public function checkExistingFamily(array $fatherData, array $motherData): Collection
        {
            // 1. البحث بالأسماء أولاً (الأكثر دقة)
            $families = $this->findMatchingFamilies($fatherData, $motherData);

            // 2. إذا لم نعثر على تطابق بالأسماء، نجرب البحث بأرقام الهاتف
            if ($families->isEmpty()) {
                $phoneMatches = collect();
                
                if (!empty($fatherData['phone'])) {
                    $phoneMatches = $phoneMatches->merge($this->findFamiliesByPhone($fatherData['phone']));
                }
                
                if (!empty($motherData['phone'])) {
                    $phoneMatches = $phoneMatches->merge($this->findFamiliesByPhone($motherData['phone']));
                }

                if ($phoneMatches->isNotEmpty()) {
                    // إزالة التكرار وتحويلها لمجموعة عائلات
                    $families = $phoneMatches->unique('id');
                    // إضافة وسم (Flag) للعائلات المطابقة بالهاتف فقط للتنبيه لاحقاً إذا لزم الأمر
                    $families->each(function($f) { $f->matched_by = 'phone'; });
                }
            }

            // تحميل العلاقات لكل عائلة
            $families->each(function ($family) {
                $family->load([
                    'guardians',
                    'students',
                    'students.status',
                    'students.city',
                    'contactDetails',
                    'contactDetails.student',
                    'contactDetails.guardian',
                ]);
            });

            return $families;
        }

        /**
         * البحث عن العائلات المرتبطة برقم هاتف معين في جداول جهات التواصل.
         */
        private function findFamiliesByPhone(string $phone): Collection
        {
            // تنظيف الرقم من أي محارف غير رقمية
            $cleanPhone = preg_replace('/\D/', '', $phone);
            if (empty($cleanPhone) || strlen($cleanPhone) < 7) return collect();

            // البحث في جدول جهات التواصل عن الرقم (في حقل phone_number أو value)
            $familyIds = ContactDetail::where('phone_number', 'like', "%{$cleanPhone}%")
                ->orWhere('value', 'like', "%{$cleanPhone}%")
                ->pluck('family_id')
                ->unique()
                ->toArray();

            if (empty($familyIds)) return collect();

            return Family::whereIn('id', $familyIds)->get();
        }

        /**
         * تسجيل طالب جديد داخل عائلة (موجودة أو جديدة).
         *
         * @param array      $data              بيانات التسجيل الكاملة
         * @param bool|null  $isConfirmed       null = بحث تلقائي، true = ربط، false = عائلة جديدة
         * @param int|null   $confirmedFamilyId معرّف العائلة المؤكدة مباشرة (يتجاوز إعادة البحث)
         */
        public function enrollStudent(array $data, ?bool $isConfirmed = null, ?int $confirmedFamilyId = null): Student
        {
            return DB::transaction(function () use ($data, $isConfirmed, $confirmedFamilyId) {

                $photoUrl   = null;
                $idCardUrl  = null;

                if (!empty($data['student']['profile_photo'])) {
                    $photoUrl = $this->fileService->uploadStudentPhoto($data['student']['profile_photo']);
                }

                if (!empty($data['student']['id_card_photo'])) {
                    $idCardUrl = $this->fileService->uploadStudentIdCard($data['student']['id_card_photo']);
                }

                // ─── الحالة 1: تأكيد ربط بعائلة موجودة ───────────────────────
                if ($isConfirmed === true) {

                    // إذا أُرسل family_id مباشرة، استخدمه فوراً بدون إعادة البحث
                    if ($confirmedFamilyId) {
                        $family = Family::find($confirmedFamilyId);
                    }

                    // fallback: إعادة البحث إذا لم يُرسل family_id
                    if (empty($family)) {
                        $matches = $this->findMatchingFamilies($data['father'], $data['mother']);
                        $family  = $matches->first();
                    }

                    // fallback نهائي: إنشاء عائلة جديدة إذا لم نجد شيئاً
                    if (!$family) {
                        Log::warning('EnrollmentService: isConfirmed=true لكن لم نعثر على عائلة، سيتم إنشاء عائلة جديدة', [
                            'father' => $data['father']['first_name'] ?? '?',
                            'confirmed_family_id' => $confirmedFamilyId,
                        ]);
                        $family = $this->createNewFamily($data['father'], $data['mother']);
                    }
                }

                // ─── الحالة 2: تأكيد إنشاء عائلة جديدة ────────────────────────
                elseif ($isConfirmed === false) {
                    $family = $this->createNewFamily($data['father'], $data['mother']);
                }

                // ─── الحالة 3: بدون تحديد — بحث تلقائي ────────────────────────
                else {
                    $matches = $this->findMatchingFamilies($data['father'], $data['mother']);
                    $family  = $matches->first() ?? $this->createNewFamily($data['father'], $data['mother']);
                }

                return Student::create(array_merge(
                    $data['student'],
                    [
                        'family_id'         => $family->id,
                        'profile_photo_url' => $photoUrl,
                        'id_card_photo_url' => $idCardUrl,
                    ]
                ));

                // تحميل العلاقات لمستقبل الخطوات (خاصة جهات الاتصال المسجلة مسبقاً للعائلة)
                $student->load([
                    'family', 
                    'family.guardians', 
                    'family.contactDetails',
                    'family.contactDetails.student',
                    'family.contactDetails.guardian',
                    'instituteBranch', 
                    'branch', 
                    'bus', 
                    'status', 
                    'city', 
                    'school'
                ]);

                return $student;
            });
        }

        // ============================================================
        // ===== الوظائف الداخلية الخاصة =============================
        // ============================================================

        /**
         * إنشاء عائلة جديدة مع أب وأم.
         */
        private function createNewFamily(array $fatherData, array $motherData): Family
        {
            $family = Family::create(['user_id' => null]);

            $father = Guardian::create([
                'family_id'          => $family->id,
                'first_name'         => $fatherData['first_name'],
                'last_name'          => $fatherData['last_name'],
                'national_id'        => $fatherData['national_id'] ?? null,
                'occupation'         => $fatherData['occupation'] ?? null,
                'address'            => $fatherData['address'] ?? null,
                'relationship'       => 'father',
                'is_primary_contact' => true,
            ]);

            // إضافة رقم هاتف الأب إن وُجد
            if (!empty($fatherData['phone'])) {
                ContactDetail::create([
                    'family_id'    => $family->id,
                    'guardian_id'  => $father->id,
                    'type'         => 'phone',
                    'value'        => $fatherData['phone'],
                    'phone_number' => $fatherData['phone'],
                    'country_code' => $fatherData['country_code'] ?? '+963',
                    'owner_type'   => 'father',
                    'owner_name'   => $fatherData['first_name'],
                    'supports_sms' => true, // الأب عادة هو الأساسي للـ SMS
                    'is_primary'   => true,
                ]);
            }

            $mother = Guardian::create([
                'family_id'          => $family->id,
                'first_name'         => $motherData['first_name'],
                'last_name'          => $motherData['last_name'],
                'national_id'        => $motherData['national_id'] ?? null,
                'occupation'         => $motherData['occupation'] ?? null,
                'address'            => $motherData['address'] ?? null,
                'relationship'       => 'mother',
                'is_primary_contact' => false,
            ]);

            // إضافة رقم هاتف الأم إن وُجد
            if (!empty($motherData['phone'])) {
                ContactDetail::create([
                    'family_id'    => $family->id,
                    'guardian_id'  => $mother->id,
                    'type'         => 'phone',
                    'value'        => $motherData['phone'],
                    'phone_number' => $motherData['phone'],
                    'country_code' => $motherData['country_code'] ?? '+963',
                    'owner_type'   => 'mother',
                    'owner_name'   => $motherData['first_name'],
                    'supports_sms' => false,
                    'is_primary'   => false,
                ]);
            }

            return $family;
        }

        /**
         * البحث عن جميع العائلات المطابقة لبيانات الأب والأم.
         *
         * التحسينات عن النسخة القديمة:
         * 1. يُطبّع الأسماء العربية قبل حساب الهاش (توحيد الألفات وإزالة التشكيل)
         * 2. يُرجع Collection من كل العائلات المتطابقة (وليس واحدة فقط)
         *
         * @param array $fatherData بيانات الأب (first_name, last_name, national_id)
         * @param array $motherData بيانات الأم (first_name, last_name, national_id)
         * @return Collection<Family>
         */
        private function findMatchingFamilies(array $fatherData, array $motherData): Collection
        {
            // ─── مرحلة 1: تطبيع وتحضير بيانات الأب ───────────────────────
            $fatherFirstName     = $this->normalizeArabicName($fatherData['first_name']);
            $fatherLastName      = $this->normalizeArabicName($fatherData['last_name']);
            $fatherNationalId    = !empty($fatherData['national_id']) ? trim($fatherData['national_id']) : null;

            $fatherFirstNameHash = sha1($fatherFirstName);
            $fatherLastNameHash  = sha1($fatherLastName);
            $fatherNationalIdHash = $fatherNationalId ? sha1($fatherNationalId) : null;

            // ─── مرحلة 2: تطبيع وتحضير بيانات الأم ────────────────────────
            $motherFirstName     = $this->normalizeArabicName($motherData['first_name']);
            $motherLastName      = $this->normalizeArabicName($motherData['last_name']);
            $motherNationalId    = !empty($motherData['national_id']) ? trim($motherData['national_id']) : null;

            $motherFirstNameHash = sha1($motherFirstName);
            $motherLastNameHash  = sha1($motherLastName);
            $motherNationalIdHash = $motherNationalId ? sha1($motherNationalId) : null;

            // ─── مرحلة 3: البحث عن الأب ────────────────────────────────────
            $fatherQuery = Guardian::where('relationship', 'father')
                ->where('first_name_hash', $fatherFirstNameHash)
                ->where('last_name_hash',  $fatherLastNameHash);

            if ($fatherNationalIdHash) {
                $fatherQuery->where('national_id_hash', $fatherNationalIdHash);
            }

            $fatherFamilyIds = $fatherQuery->pluck('family_id')->unique()->toArray();

            if (empty($fatherFamilyIds)) {
                return collect();
            }

            // ─── مرحلة 4: البحث عن الأم ضمن عائلات الأب ───────────────────
            $motherQuery = Guardian::where('relationship', 'mother')
                ->where('first_name_hash', $motherFirstNameHash)
                ->where('last_name_hash',  $motherLastNameHash)
                ->whereIn('family_id', $fatherFamilyIds);

            if ($motherNationalIdHash) {
                $motherQuery->where('national_id_hash', $motherNationalIdHash);
            }

            // جمع كل معرّفات العائلات المتطابقة (قد تكون أكثر من واحدة!)
            $matchedFamilyIds = $motherQuery->pluck('family_id')->unique()->toArray();

            if (empty($matchedFamilyIds)) {
                return collect();
            }

            // إرجاع كل العائلات المتطابقة كـ Collection
            return Family::whereIn('id', $matchedFamilyIds)->get();
        }
    }
