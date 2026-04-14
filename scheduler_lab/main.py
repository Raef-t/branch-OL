import json
import os
from core.model import create_model
from core.variables import define_variables
from core.constraints import add_hard_constraints
from core.preferences import add_soft_constraints
from core.empty_slots import add_empty_slot_constraints
from core.objective import set_objective
from solver.run_solver import solve_model
from presentation.printer import export_to_excel

def print_solution(solution, data):
    """طباعة حل في الطرفية بشكل منسق مع دعم الفروع"""
    print("\n" + "="*80)
    print("🏆 أفضل حل وجد")
    print("="*80)
    
    # تنظيم الحل حسب الفروع ثم الصفوف ثم الأيام
    organized = {}
    
    # الحصول على تفاصيل الفروع والصفوف
    class_details = {}
    for branch in data['branches']:
        for cls in branch['classes']:
            class_details[cls['id']] = {
                'name': cls['name'],
                'branch_name': branch['name']
            }
    
    unassigned_lessons = []
    
    for key, value in solution.items():
        if isinstance(key, tuple):
            (cls_id, sub_id, day, slot_id) = key
            if value == 1:
                cls_info = class_details.get(cls_id, {'name': f'صف {cls_id}', 'branch_name': 'غير معروف'})
                
                if cls_info['branch_name'] not in organized:
                    organized[cls_info['branch_name']] = {}
                
                if cls_info['name'] not in organized[cls_info['branch_name']]:
                    organized[cls_info['branch_name']][cls_info['name']] = {}
                
                if day not in organized[cls_info['branch_name']][cls_info['name']]:
                    organized[cls_info['branch_name']][cls_info['name']][day] = []
                
                organized[cls_info['branch_name']][cls_info['name']][day].append((slot_id, sub_id))
        
        elif isinstance(key, str) and key.startswith("unassigned_var_") and value > 0:
            parts = key.split("_")
            if len(parts) >= 4:
                unassigned_lessons.append((parts[2], parts[3], value)) # cls_id, sub_id, count
    
    # طباعة الحل حسب الفروع
    for branch_name in sorted(organized.keys()):
        print(f"\n\n📚 {branch_name.upper()}")
        print("=" * 60)
        
        for class_name in sorted(organized[branch_name].keys()):
            print(f"\n🏫 {class_name}")
            print("-" * 40)
            
            for day in sorted(organized[branch_name][class_name].keys()):
                print(f"\n📅 {day.upper()}")
                
                slots = sorted(organized[branch_name][class_name][day])
                for slot_id, sub_id in slots:
                    # الحصول على تفاصيل المادة
                    subject_name = "غير معروف"
                    teacher_name = "غير معروف"
                    
                    for branch in data['branches']:
                        for cls in branch['classes']:
                            if cls['name'] == class_name:
                                for sub in cls['subjects']:
                                    if sub['id'] == sub_id:
                                        subject_name = sub['name']
                                        teacher_id = sub['teacher_ids'][0] if sub['teacher_ids'] else None
                                        if teacher_id:
                                            teacher = next((t for t in data['teachers'] if t['id'] == teacher_id), None)
                                            teacher_name = teacher['name'] if teacher else f"معلم {teacher_id}"
                                        break
                    
                    # الحصول على وقت الحصة
                    slot = next((s for s in data['slots'] if s['id'] == slot_id), None)
                    slot_label = slot['label'] if slot else f"الحصة {slot_id}"
                    
                    print(f"  ⏰ {slot_label}: {subject_name} (المعلم: {teacher_name})")
    
    # طباعة الحصص المعلقة إن وجدت
    if unassigned_lessons:
        print("\n\n" + "🚨"*40)
        print("⚠️ تحذير خطير: حصص فشل النظام في جدولتها (تتطلب تدخل بشري)")
        print("🚨"*40)
        for cls_id, sub_id, count in unassigned_lessons:
            subject_name = "غير معروف"
            class_name = f"صف {cls_id}"
            for branch in data['branches']:
                for cls in branch['classes']:
                    if cls['id'] == cls_id:
                        class_name = cls['name']
                        for sub in cls['subjects']:
                            if sub['id'] == sub_id:
                                subject_name = sub['name']
                                break
            
            print(f"  ❌ مادة [{subject_name}] في [{class_name}]: {count} حصة/حصص لم تُجدول بسبب التعارضات!")

def print_input_summary(data, config):
    """طباعة ملخص المدخلات في الطرفية مع دعم الفروع وتفضيلات كاملة"""
    print("\n" + "="*80)
    print("📋 ملخص المدخلات الكامل")
    print("="*80)
    
    # طباعة إعدادات التكوين بالتفصيل
    print(f"\n⚙️ إعدادات التكوين المتقدمة:")
    print(f"  - السماح بالفترات الفارغة: {'✅ نعم' if config.get('allow_empty_slots', False) else '❌ لا'}")
    if config.get('allow_empty_slots', False):
        print(f"  - عقوبة كل فترة فارغة: {config.get('empty_slot_penalty', 5)}")
    print(f"  - عدد الحلول المطلوبة: {config.get('max_solutions_to_generate', 3)}")
    print(f"  - وقت الحساب الأقصى: {config.get('time_limit_seconds', 60)} ثانية")
    print(f"  - عدد العاملين (Workers): {config.get('solver_workers', 8)}")
    
    # طباعة الأيام
    print(f"\n✅ الأيام المستخدمة ({len(data['days'])}):")
    print(", ".join(day.upper() for day in data['days']))
    
    # طباعة الفترات الزمنية
    print(f"\n✅ الفترات الزمنية ({len(data['slots'])}):")
    for slot in data['slots']:
        print(f"  ⏱️ الفترة {slot['id']}: {slot['label']}")
    
    # طباعة الغرف
    print(f"\n✅ الغرف ({len(data['rooms'])}):")
    for room in data['rooms']:
        print(f"  🏫 الغرفة {room['id']}: {room['name']}")
    
    # 📌 إضافة جديدة: تفصيل المواد لكل مدرس
    print(f"\n📚 المواد التي يدرسها كل مدرس:")
    teacher_subjects = {}
    
    # جمع المواد لكل مدرس
    for branch in data['branches']:
        for cls in branch['classes']:
            for sub in cls['subjects']:
                for teacher_id in sub['teacher_ids']:
                    if teacher_id not in teacher_subjects:
                        teacher = next((t for t in data['teachers'] if t['id'] == teacher_id), None)
                        teacher_name = teacher['name'] if teacher else f"مدرس {teacher_id}"
                        teacher_subjects[teacher_id] = {
                            'name': teacher_name,
                            'subjects': []
                        }
                    
                    # إضافة المادة إذا لم تكن موجودة
                    subject_info = {
                        'name': sub['name'],
                        'class': cls['name'],
                        'branch': branch['name'],
                        'lessons': sub['lessons_per_week']
                    }
                    
                    # التحقق من عدم التكرار
                    exists = False
                    for existing in teacher_subjects[teacher_id]['subjects']:
                        if existing['name'] == subject_info['name'] and existing['class'] == subject_info['class']:
                            exists = True
                            break
                    
                    if not exists:
                        teacher_subjects[teacher_id]['subjects'].append(subject_info)
    
    # طباعة التفاصيل
    for teacher_id, info in teacher_subjects.items():
        print(f"\n  👨‍🏫 {info['name']} (ID: {teacher_id})")
        print("    المواد:")
        
        total_lessons = 0
        for sub in info['subjects']:
            print(f"      • {sub['name']} في {sub['class']} ({sub['branch']}): {sub['lessons']} حصة/اسبوع")
            total_lessons += sub['lessons']
        
        print(f"    📊 المجموع: {total_lessons} حصة/اسبوع")
    
    # طباعة الأساتذة مع تفضيلاتهم الكاملة
    print(f"\n✅ الأساتذة ({len(data['teachers'])}):")
    for teacher in data['teachers']:
        print(f"\n  👨‍🏫 {teacher['name']} (ID: {teacher['id']})")
        
        # تفصيل التفضيلات
        prefs = teacher.get('preferences', {})
        if prefs:
            print(f"    📌 التفضيلات المفصلة:")
            
            # الأيام المفضلة
            preferred_days = prefs.get('preferred_days', [])
            if preferred_days:
                print(f"      • الأيام المفضلة: {', '.join(preferred_days)} (الثقل: 10 لكل يوم)")
            
            # الأيام المراد تجنبها
            avoid_days = prefs.get('avoid_days', [])
            if avoid_days:
                print(f"      • الأيام المراد تجنبها: {', '.join(avoid_days)} (الثقل: 20 لكل يوم)")
            
            # الفترات المفضلة
            preferred_slots = prefs.get('preferred_slots', [])
            if preferred_slots:
                slot_labels = [next((s['label'] for s in data['slots'] if s['id'] == sid), f"الحصة {sid}") for sid in preferred_slots]
                print(f"      • الفترات المفضلة: {', '.join(slot_labels)} (الثقل: 5 لكل فترة)")
            
            # الفترات المراد تجنبها
            avoid_slots = prefs.get('avoid_slots', [])
            if avoid_slots:
                slot_labels = [next((s['label'] for s in data['slots'] if s['id'] == sid), f"الحصة {sid}") for sid in avoid_slots]
                print(f"      • الفترات المراد تجنبها: {', '.join(slot_labels)} (الثقل: 10 لكل فترة)")
        else:
            print("    • لا توجد تفضيلات محددة")
    
    # طباعة الفروع والصفوف
    print(f"\n✅ الفروع ({len(data['branches'])}):")
    
    for branch in data['branches']:
        print(f"\n  🌿 {branch['name']} (ID: {branch['id']})")
        print(f"    عدد الصفوف: {len(branch['classes'])}")
        
        for cls in branch['classes']:
            room = next((r for r in data['rooms'] if r['id'] == cls['room_id']), None)
            room_name = room['name'] if room else f"الغرفة {cls['room_id']}"
            print(f"\n      📚 {cls['name']} (الغرفة: {room_name})")
            print("        المواد:")
            for sub in cls['subjects']:
                teachers = []
                for tid in sub['teacher_ids']:
                    teacher = next((t for t in data['teachers'] if t['id'] == tid), None)
                    teachers.append(teacher['name'] if teacher else f"معلم {tid}")
                teacher_names = ", ".join(teachers)
                print(f"          • {sub['name']}: {sub['lessons_per_week']} حصة/اسبوع")
                print(f"            المعلمون: {teacher_names}")
                print(f"            ثقل المادة في الخطة: {config.get('subject_priority', {}).get(sub['name'].lower(), 1)}")
    
    # 📌 إضافة جديدة: تقرير شامل عن الأيام والأوقات
    print(f"\n⏰ تقرير الأيام والأوقات:")
    print(f"  • أيام العمل: {', '.join(day.upper() for day in data['days'])}")
    print(f"  • عدد الفترات اليومية: {len(data['slots'])}")
    print(f"  • إجمالي الفترات الأسبوعية: {len(data['days']) * len(data['slots'])}")
    print(f"  • الفترات:")
    for slot in data['slots']:
        print(f"    - {slot['label']}")
    
    # ملخص العقوبات في دالة الهدف
    print(f"\n📊 ملخص العقوبات في دالة الهدف:")
    print(f"  • عقوبة الفترات الفارغة: {config.get('empty_slot_penalty', 5)} لكل فترة")
    print(f"  • عقوبة الأيام غير المفضلة: 10 لكل يوم")
    print(f"  • عقوبة الأيام المراد تجنبها: 20 لكل يوم")
    print(f"  • عقوبة الفترات غير المفضلة: 5 لكل فترة")
    print(f"  • عقوبة الفترات المراد تجنبها: 10 لكل فترة")
    
    print("\n" + "="*80)

def main():
    # تحميل إعدادات التكوين
    try:
        config_path = os.path.join('config', 'default_config.json')
        with open(config_path, 'r', encoding='utf-8') as f:
            config = json.load(f)
        print("✅ تم تحميل إعدادات التكوين بنجاح")
    except FileNotFoundError:
        print(f"❌ خطأ: ملف التكوين {config_path} غير موجود")
        print("الحل: تأكد من وجود مجلد 'config' وملف 'default_config.json'")
        return
    except json.JSONDecodeError as e:
        print(f"❌ خطأ في تنسيق ملف التكوين: {e}")
        return
    
    # تحميل البيانات من JSON
    try:
        input_path = os.path.join('data', 'input.json')
        with open(input_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
        print("✅ تم تحميل بيانات الجدول الزمني بنجاح")
    except FileNotFoundError:
        print(f"❌ خطأ: ملف {input_path} غير موجود")
        return
    except json.JSONDecodeError as e:
        print(f"❌ خطأ في تنسيق ملف JSON: {e}")
        return
    
    # ✅ التصحيح الرئيسي هنا: إضافة 'data' بعد 'field not in'
    required_fields = ['branches', 'teachers', 'rooms', 'slots', 'days']
    for field in required_fields:
        if field not in data:
            print(f"❌ خطأ: الحقل '{field}' مفقود في ملف input.json")
            return
    
    # التحقق من كل مادة لديها lessons_per_week و teacher_ids
    validation_errors = []
    for branch in data['branches']:
        for cls in branch['classes']:
            for sub in cls['subjects']:
                if 'lessons_per_week' not in sub:
                    validation_errors.append(f"المادة {sub['name']} في {cls['name']} تفتقر إلى 'lessons_per_week'")
                if 'teacher_ids' not in sub:
                    validation_errors.append(f"المادة {sub['name']} في {cls['name']} تفتقر إلى 'teacher_ids'")
    
    if validation_errors:
        print("❌ أخطاء في التحقق من البيانات:")
        for error in validation_errors:
            print(f"  - {error}")
        return
    
    print("✅ التحقق من صحة المدخلات: نجاح")
    
    # إنشاء النموذج
    model = create_model()
    
    # تعريف المتغيرات
    variables = define_variables(model, data)
    
    # إضافة القيود الصلبة (ومعها قيد الحصص المعلقة المرن)
    unassigned_penalties = add_hard_constraints(model, variables, data, config)
    
    # إضافة قيود الفترات الفارغة (الخطوة الأولى للتطوير)
    empty_vars, empty_penalties = add_empty_slot_constraints(model, variables, data, config)
    
    # إضافة التفضيلات (Soft Constraints) وجمع العقوبات
    soft_penalties = add_soft_constraints(model, variables, data, config)
    
    # دمج جميع العقوبات
    all_penalties = soft_penalties + empty_penalties + unassigned_penalties
    
    # تحديد دالة الهدف
    set_objective(model, all_penalties)
    
    # حل النموذج
    print("\n🔍 جاري حل النموذج...")
    solutions = solve_model(model, variables, data, config)
    
    if solutions:
        print(f"\n✅ تم العثور على {len(solutions)} حلول")
        
        # طباعة أفضل حل
        best_solution = solutions[0][1]  # نأخذ الحل نفسه (العنصر الثاني في الزوج)
        print_solution(best_solution, data)
        
        # طباعة تقرير المدخلات
        print("\n📊 ملخص المدخلات:")
        print_input_summary(data, config)
        
        # تصدير الحلول إلى Excel
        print("\n📤 تصدير الحلول إلى ملف Excel...")
        export_to_excel([sol for _, sol in solutions], data, config)
    else:
        print("\n❌ لم يتم العثور على حلول! الأسباب المحتملة:")
        print("  - تناقض في القيود (مثل: عدد الحصص المطلوبة لا يتناسب مع الوقت المتاح)")
        print("  - تفضيلات الأساتذة تتعارض مع القيود الأساسية")
        print("  - حاول تقليل عدد الحصص أو تعديل التفضيلات")
        print("  - ملاحظة: إذا كنت تستخدم الفترات الفارغة، قد تحتاج لزيادة عددها أو تقليل العقوبات")

if __name__ == "__main__":
    main()