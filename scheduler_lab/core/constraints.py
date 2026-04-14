def add_same_subject_same_day_constraints(model, variables, data, config=None):
    """إضافة قيود تكرار المادة في نفس اليوم (دعم التخصيص على مستوى المادة والصف)"""
    
    total_constraints = 0
    config = config or {}
    
    for branch in data['branches']:
        for cls in branch['classes']:
            for sub in cls['subjects']:
                # الإعداد العام يعمل كمفتاح رئيسي
                global_allow = config.get('default_allow_same_subject_same_day', False)
                
                if not global_allow:
                    subject_allow_same = False
                    source = "التبديل العام (تعطيل قسري)"
                else:
                    subject_allow_same = sub.get('allow_same_subject_same_day', 
                                               cls.get('allow_same_subject_same_day', True))
                    source = "المادة" if 'allow_same_subject_same_day' in sub else \
                            ("الصف" if 'allow_same_subject_same_day' in cls else "التكوين العام")
                
                if not subject_allow_same:
                    print(f"  ➤ منع تكرار المادة '{sub['name']}' في الصف: {cls['name']} (المصدر: {source})")
                    
                    for day in data['days']:
                        subject_day_vars = []
                        
                        for slot in data['slots']:
                            key = (cls['id'], sub['id'], day, slot['id'])
                            if key in variables:
                                subject_day_vars.append(variables[key])
                        
                        # إضافة القيد فقط إذا كانت هناك متغيرات متعددة لهذه المادة في اليوم
                        if subject_day_vars and len(subject_day_vars) > 1:
                            model.Add(sum(subject_day_vars) <= 1)
                            total_constraints += 1
    
    print(f"✅ أُضيفت {total_constraints} قيود لمنع تكرار المواد في نفس اليوم")

def add_hard_constraints(model, variables, data, config=None):
    """إضافة القيود، مع تحويل قيد اكتمال الحصص ليكون ليناً (Soft) بغرامات فلكية لتجنب التعارض المطلق"""
    config = config or {}
    unassigned_penalties = []
    
    # 1. كل صف لا يمكنه أخذ أكثر من حصة في نفس الوقت
    for branch in data['branches']:
        for cls in branch['classes']:
            for day in data['days']:
                for slot in data['slots']:
                    model.Add(
                        sum(
                            variables[(cls['id'], sub['id'], day, slot['id'])]
                            for sub in cls['subjects']
                        ) <= 1
                    )
    
    # 2. كل أستاذ لا يمكنه التدريس في فصلين بنفس الوقت
    teacher_assignments = {}
    
    for branch in data['branches']:
        for cls in branch['classes']:
            for sub in cls['subjects']:
                for teacher_id in sub['teacher_ids']:
                    for day in data['days']:
                        for slot in data['slots']:
                            key = (teacher_id, day, slot['id'])
                            var_key = (cls['id'], sub['id'], day, slot['id'])
                            
                            if var_key in variables:
                                if key not in teacher_assignments:
                                    teacher_assignments[key] = []
                                teacher_assignments[key].append(variables[var_key])
    
    for key, vars_list in teacher_assignments.items():
        model.Add(sum(vars_list) <= 1)
    
    # 3. احتمال الحصص المعلقة: تحويل قيد عدد الحصص المطلوبة إلى قيد مرن لتجنب INFEASIBLE
    for branch in data['branches']:
        for cls in branch['classes']:
            for sub in cls['subjects']:
                total_required = sub['lessons_per_week']
                assigned_vars = []
                
                for day in data['days']:
                    for slot in data['slots']:
                        key = (cls['id'], sub['id'], day, slot['id'])
                        if key in variables:
                            assigned_vars.append(variables[key])
                
                # إنشاء متغير للحصص المعلقة (Unassigned)
                var_name = f"unassigned_{cls['id']}_{sub['id']}"
                unassigned_var = model.NewIntVar(0, total_required, var_name)
                # حفظه في قاموس variables لطباعته واستخراجه لاحقاً
                variables[f"unassigned_var_{cls['id']}_{sub['id']}"] = unassigned_var
                
                # تعديل القيد: الحصص المجدولة + الحصص المعلقة = المطلوب
                model.Add(sum(assigned_vars) + unassigned_var == total_required)
                
                # عقوبة فلكية (مليون نقطة لكل حصة معلقة)
                penalty = model.NewIntVar(0, 1000000 * total_required, f"penalty_{var_name}")
                model.Add(penalty == unassigned_var * 1000000)
                unassigned_penalties.append(penalty)
    
    # 4. قيود الغرف: كل غرفة لا يمكنها استضافة أكثر من فصل في نفس الوقت
    room_assignments = {}
    
    for branch in data['branches']:
        for cls in branch['classes']:
            room_id = cls['room_id']
            for day in data['days']:
                for slot in data['slots']:
                    key = (room_id, day, slot['id'])
                    room_vars = []
                    
                    for sub in cls['subjects']:
                        var_key = (cls['id'], sub['id'], day, slot['id'])
                        if var_key in variables:
                            room_vars.append(variables[var_key])
                    
                    if key not in room_assignments:
                        room_assignments[key] = []
                    room_assignments[key].append(sum(room_vars))
    
    for key, expr_list in room_assignments.items():
        model.Add(sum(expr_list) <= 1)
    
    # 5. ⭐ إضافة قيود تكرار المادة في نفس اليوم (الدعم الجديد)
    add_same_subject_same_day_constraints(model, variables, data, config)
    
    # 6. 🚫 قيود الأوقات المحظورة للمدرسين (blocked_slots)
    add_blocked_slots_constraints(model, variables, data)
    
    print("✅ أُضيفت جميع القيود الصلبة الأساسية، والقيد المرن للحصص المعلقة")
    return unassigned_penalties


def add_blocked_slots_constraints(model, variables, data):
    """
    إضافة قيود صلبة تمنع جدولة المدرس في الأوقات المحظورة.
    blocked_slots في تفضيلات المدرس: {"day": [slot_ids]}
    هذا قيد صارم (Hard Constraint) لا يمكن تجاوزه أبداً.
    """
    total_constraints = 0
    
    for teacher in data['teachers']:
        prefs = teacher.get('preferences', {})
        blocked = prefs.get('blocked_slots', {})
        teacher_id = teacher['id']
        
        if not blocked:
            continue
        
        for day, slot_ids in blocked.items():
            for slot_id in slot_ids:
                # منع جميع مواد هذا المدرس في هذا الوقت المحظور
                for branch in data['branches']:
                    for cls in branch['classes']:
                        for sub in cls['subjects']:
                            if teacher_id in sub['teacher_ids']:
                                key = (cls['id'], sub['id'], day, slot_id)
                                if key in variables:
                                    model.Add(variables[key] == 0)
                                    total_constraints += 1
        
        if blocked:
            print(f"  🚫 أستاذ {teacher['name']}: {total_constraints} وقت محظور")
    
    print(f"✅ أُضيفت {total_constraints} قيود للأوقات المحظورة")