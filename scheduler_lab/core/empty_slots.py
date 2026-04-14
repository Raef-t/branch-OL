from ortools.sat.python import cp_model

def add_empty_slot_constraints(model, variables, data, config):
    """
    إضافة قيود الفترات الفارغة:
    - لكل فصل في كل يوم وفي كل فترة زمنية، يمكن أن تكون فارغة
    - إذا كان 'allow_empty_slots' معطلاً، يتم فرض "التتابع" (Contiguity) لمنع الفجوات في منتصف اليوم.
    """
    empty_slot_vars = {}
    penalties = []
    
    print("✅ إضافة قيود الفترات الفارغة والتتابع...")
    
    for branch in data.get('branches', []):
        for cls in branch['classes']:
            class_day_occupied_vars = []
            
            for day in data['days']:
                day_occupancy_vars = []
                
                # 1. تجميع المتغيرات لكل اليوم لضمان التتابع الداخلي (داخل اليوم)
                for slot in data['slots']:
                    var_name = f"empty_{cls['id']}_{day}_{slot['id']}"
                    empty_var = model.NewBoolVar(var_name)
                    empty_slot_vars[(cls['id'], day, slot['id'])] = empty_var
                    
                    subject_vars = []
                    for sub in cls['subjects']:
                        key = (cls['id'], sub['id'], day, slot['id'])
                        if key in variables:
                            subject_vars.append(variables[key])
                    
                    if subject_vars:
                        # sum(subject_vars) + empty_var == 1
                        model.Add(sum(subject_vars) + empty_var == 1)
                        is_occupied = empty_var.Not()
                        day_occupancy_vars.append(is_occupied)
                    else:
                        model.Add(empty_var == 1)
                        day_occupancy_vars.append(cp_model.Literal(0))

                # قيد التتابع داخل اليوم (الحصة i مشغولة => الحصة i-1 مشغولة)
                if not config.get("allow_empty_slots", False):
                    for i in range(1, len(day_occupancy_vars)):
                        model.Add(day_occupancy_vars[i] <= day_occupancy_vars[i-1])
                
                # تتبع ما إذا كان الكلاس "مشغولاً" في هذا اليوم
                day_is_occupied = model.NewBoolVar(f"day_occ_{cls['id']}_{day}")
                # day_is_occupied == 1 if any day_occupancy_vars == 1
                model.AddMaxEquality(day_is_occupied, day_occupancy_vars)
                class_day_occupied_vars.append(day_is_occupied)

                # إضافة عقوبات في حال السماح بالفراغات
                if config.get("allow_empty_slots", False):
                    penalty_weight = config.get("empty_slot_penalty", 5)
                    for slot in data['slots']:
                        empty_var = empty_slot_vars[(cls['id'], day, slot['id'])]
                        p_var = model.NewIntVar(0, penalty_weight, f"p_empty_{cls['id']}_{day}_{slot['id']}")
                        model.Add(p_var == penalty_weight).OnlyEnforceIf(empty_var)
                        model.Add(p_var == 0).OnlyEnforceIf(empty_var.Not())
                        penalties.append(p_var)

            # 2. تطبيق قيد التتابع بين الأيام (إذا طلب المستخدم منع الفراغات)
            # اليوم d مشغولة => اليوم d-1 مشغولة
            if not config.get("allow_empty_slots", False):
                for d in range(1, len(class_day_occupied_vars)):
                    model.Add(class_day_occupied_vars[d] <= class_day_occupied_vars[d-1])
    
    if not config.get("allow_empty_slots", False):
        print("  ➤ تم تفعيل نظام التتابع للأيام والحصص (Weekly & Daily Contiguity)")
    else:
        print(f"  ➤ أُضيفت {len(penalties)} عقوبة لتقليل الفترات الفارغة")
        
    return empty_slot_vars, penalties
