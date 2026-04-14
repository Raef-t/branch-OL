def add_soft_constraints(model, variables, data, config):
    """
    إضافة التفضيلات كـ Soft Constraints مع دعم الأولويات والفترات المحظورة
    """
    penalties = []
    
    # ⚠️ التصحيح الرئيسي هنا: تأكد من وجود الأوزان الأساسية
    base_weights = config.get('teacher_preference_weights', {})
    
    # توفير قيم افتراضية كاملة إذا كانت الأوزان غير موجودة
    if not base_weights:
        base_weights = {
            'preferred_days': 10,    # ملاحظة:_plural (days)
            'avoid_days': 20,        #_plural (days)
            'preferred_slots': 5,    #_plural (slots)
            'avoid_slots': 10        #_plural (slots)
        }
        print("⚠️ لم يتم العثور على 'teacher_preference_weights' في التكوين. استخدام القيم الافتراضية.")
    
    # الحصول على مستويات الأولوية من التكوين
    priority_levels = config.get('teacher_priority_levels', {})
    
    for teacher in data['teachers']:
        # الحصول على مستوى الأولوية والمضاعف
        priority_level = teacher.get('priority_level', 2)
        level_info = priority_levels.get(priority_level, {})
        multiplier = level_info.get('weight_multiplier', 1.0)
        
        prefs = teacher.get('preferences', {})
        teacher_id = teacher['id']
        
        # 1. الأيام المفضلة
        for day in prefs.get('preferred_days', []):
            # جمع جميع الحصص التي يقوم بها الأستاذ في هذا اليوم
            teacher_day_assignments = []
            
            for branch in data['branches']:
                for cls in branch['classes']:
                    for sub in cls['subjects']:
                        if teacher_id in sub['teacher_ids']:
                            for slot in data['slots']:
                                key = (cls['id'], sub['id'], day, slot['id'])
                                if key in variables:
                                    teacher_day_assignments.append(variables[key])
            
            if teacher_day_assignments:
                # متغير عقوبة: 1 إذا لم يُخصص الأستاذ في هذا اليوم
                penalty_var = model.NewBoolVar(f"penalty_pref_day_{teacher_id}_{day}")
                model.Add(sum(teacher_day_assignments) == 0).OnlyEnforceIf(penalty_var)
                model.Add(sum(teacher_day_assignments) > 0).OnlyEnforceIf(penalty_var.Not())
                
                # حساب الوزن المعدل
                base_weight = base_weights.get('preferred_days', 10)  #_plural
                weight = base_weight * multiplier
                
                # عقوبة
                weighted_penalty = model.NewIntVar(0, int(weight), f"weighted_penalty_pref_day_{teacher_id}_{day}")
                model.Add(weighted_penalty == int(weight) * penalty_var)
                penalties.append(weighted_penalty)
                print(f"  ➤ أستاذ {teacher['name']} (أولوية {priority_level}): عقوبة {weight:.1f} ليوم {day} المفضل")
        
        # 2. الأيام المراد تجنبها
        for day in prefs.get('avoid_days', []):
            teacher_day_assignments = []
            
            for branch in data['branches']:
                for cls in branch['classes']:
                    for sub in cls['subjects']:
                        if teacher_id in sub['teacher_ids']:
                            for slot in data['slots']:
                                key = (cls['id'], sub['id'], day, slot['id'])
                                if key in variables:
                                    teacher_day_assignments.append(variables[key])
            
            if teacher_day_assignments:
                penalty_var = model.NewBoolVar(f"penalty_avoid_day_{teacher_id}_{day}")
                model.Add(sum(teacher_day_assignments) > 0).OnlyEnforceIf(penalty_var)
                model.Add(sum(teacher_day_assignments) == 0).OnlyEnforceIf(penalty_var.Not())
                
                base_weight = base_weights.get('avoid_days', 20)  #_plural
                weight = base_weight * multiplier
                
                weighted_penalty = model.NewIntVar(0, int(weight), f"weighted_penalty_avoid_day_{teacher_id}_{day}")
                model.Add(weighted_penalty == int(weight) * penalty_var)
                penalties.append(weighted_penalty)
                print(f"  ➤ أستاذ {teacher['name']} (أولوية {priority_level}): عقوبة {weight:.1f} لتجنب يوم {day}")
        
        # 3. الفترات المفضلة
        for slot_id in prefs.get('preferred_slots', []):
            for day in data['days']:
                teacher_slot_assignments = []
                
                for branch in data['branches']:
                    for cls in branch['classes']:
                        for sub in cls['subjects']:
                            if teacher_id in sub['teacher_ids']:
                                key = (cls['id'], sub['id'], day, slot_id)
                                if key in variables:
                                    teacher_slot_assignments.append(variables[key])
                
                if teacher_slot_assignments:
                    penalty_var = model.NewBoolVar(f"penalty_pref_slot_{teacher_id}_{day}_{slot_id}")
                    model.Add(sum(teacher_slot_assignments) == 0).OnlyEnforceIf(penalty_var)
                    model.Add(sum(teacher_slot_assignments) > 0).OnlyEnforceIf(penalty_var.Not())
                    
                    base_weight = base_weights.get('preferred_slots', 5)  #_plural
                    weight = base_weight * multiplier
                    
                    weighted_penalty = model.NewIntVar(0, int(weight), f"weighted_penalty_pref_slot_{teacher_id}_{day}_{slot_id}")
                    model.Add(weighted_penalty == int(weight) * penalty_var)
                    penalties.append(weighted_penalty)
                    print(f"  ➤ أستاذ {teacher['name']} (أولوية {priority_level}): عقوبة {weight:.1f} لفترة {slot_id} المفضلة في يوم {day}")
        
        # 4. الفترات المراد تجنبها
        for slot_id in prefs.get('avoid_slots', []):
            for day in data['days']:
                teacher_slot_assignments = []
                
                for branch in data['branches']:
                    for cls in branch['classes']:
                        for sub in cls['subjects']:
                            if teacher_id in sub['teacher_ids']:
                                key = (cls['id'], sub['id'], day, slot_id)
                                if key in variables:
                                    teacher_slot_assignments.append(variables[key])
                
                if teacher_slot_assignments:
                    penalty_var = model.NewBoolVar(f"penalty_avoid_slot_{teacher_id}_{day}_{slot_id}")
                    model.Add(sum(teacher_slot_assignments) > 0).OnlyEnforceIf(penalty_var)
                    model.Add(sum(teacher_slot_assignments) == 0).OnlyEnforceIf(penalty_var.Not())
                    
                    base_weight = base_weights.get('avoid_slots', 10)  #_plural
                    weight = base_weight * multiplier
                    
                    weighted_penalty = model.NewIntVar(0, int(weight), f"weighted_penalty_avoid_slot_{teacher_id}_{day}_{slot_id}")
                    model.Add(weighted_penalty == int(weight) * penalty_var)
                    penalties.append(weighted_penalty)
                    print(f"  ➤ أستاذ {teacher['name']} (أولوية {priority_level}): عقوبة {weight:.1f} لتجنب فترة {slot_id} في يوم {day}")
    
    print(f"✅ أُضيفت {len(penalties)} عقوبة للتفضيلات")
    return penalties