def define_variables(model, data):
    """
    تعريف متغيرات القرار:
    - متغيرات ثنائية لكل (فصل، مادة، يوم، فترة)
    """
    variables = {}
    
    for branch in data['branches']:
        for cls in branch['classes']:
            for subject in cls['subjects']:
                for day in data['days']:
                    for slot in data['slots']:
                        var_name = f"x_{cls['id']}_{subject['id']}_{day}_{slot['id']}"
                        variables[(cls['id'], subject['id'], day, slot['id'])] = model.NewBoolVar(var_name)
    
    print(f"✅ عُرّفت {len(variables)} متغيرات")
    return variables