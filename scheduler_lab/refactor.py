import sys, os

def refactor_app():
    file_path = "app_preferences.py"
    with open(file_path, "r", encoding="utf-8") as f:
        lines = f.readlines()

    # Find where to inject the config loading functions
    inject_idx = -1
    for i, line in enumerate(lines):
        if line.startswith("def get_venv_python_path()"):
            inject_idx = i
            break
            
    if inject_idx == -1:
        print("Could not find get_venv_python_path")
        return

    config_funcs = """def get_config_path():
    project_root = __import__('os').path.dirname(__import__('os').path.abspath(__file__))
    return __import__('os').path.join(project_root, "config", "default_config.json")

def load_config():
    try:
        with open(get_config_path(), 'r', encoding='utf-8') as f:
            return __import__('json').load(f)
    except Exception:
        return {}

def save_config(cd):
    try:
        with open(get_config_path(), 'w', encoding='utf-8') as f:
            __import__('json').dump(cd, f, ensure_ascii=False, indent=2)
        return True
    except Exception as e:
        __import__('streamlit').error(f"❌ خطأ: {e}")
        return False

"""
    lines.insert(inject_idx, config_funcs)
    
    # Re-read lines array conceptually by joining and splitting again to avoid index shifting math later
    text = "".join(lines)
    lines = text.splitlines(True)
    
    # Find start of UI 
    start_idx = -1
    for i, line in enumerate(lines):
        if line.startswith("    # ====== دليل الألوان ======"):
            start_idx = i
            break
            
    if start_idx == -1:
        print("Could not find start of UI")
        return

    # Add tabs and layout
    tabs_code = """    config_data = load_config()

    tab_prefs, tab_structure, tab_config = st.tabs([
        "👨‍🏫 بناء الجداول (تفضيلات الأساتذة)",
        "🏫 هيكل المدرسة (الفروع والشعب والمواد)",
        "⚙️ إعدادات الخوارزمية"
    ])

    with tab_structure:
        st.markdown("### 🏫 الهيكل التنظيمي للمدرسة")
        if not data.get('branches'):
            st.info("لا توجد فروع مسجلة.")
        for branch in data.get('branches', []):
            with st.expander(f"📍 الفرع / المسار: {branch['name']}", expanded=False):
                for cls in branch.get('classes', []):
                    st.markdown(f"#### 📚 {cls['name']} (الغرفة: {next((r['name'] for r in data.get('rooms', []) if r['id'] == cls.get('room_id')), 'غير محددة')})")
                    if cls.get('subjects'):
                        for sub in cls['subjects']:
                            teachers_names = [next((t['name'] for t in data['teachers'] if t['id'] == tid), "?") for tid in sub.get('teacher_ids', [])]
                            st.markdown(f"- **{sub['name']}**: {sub['lessons_per_week']} حصة/أسبوع (المدرسون: {', '.join(teachers_names)})")
                    else:
                        st.caption("لا توجد مواد مسجلة لهذا الصف.")

    with tab_config:
        st.markdown("### ⚙️ إعدادات الخوارزمية")
        col1, col2 = st.columns(2)
        with col1:
            max_sol = st.number_input("الحد الأقصى للحلول المولدة:", min_value=1, max_value=20, value=config_data.get('max_solutions_to_generate', 3))
            allow_empty = st.checkbox("السماح بفترات فارغة (Gap)", value=config_data.get('allow_empty_slots', True))
            default_allow_same = st.checkbox("السماح بتكرار المادة في نفس اليوم (افتراضي)", value=config_data.get('default_allow_same_subject_same_day', False))
        with col2:
            time_lim = st.number_input("الحد الأقصى لزمن البحث (ثانية):", min_value=10, max_value=600, value=config_data.get('time_limit_seconds', 60))
            empty_pen = st.number_input("عقوبة كل فترة فارغة للمدرس:", min_value=0, max_value=100, value=config_data.get('empty_slot_penalty', 5))
            solver_w = st.number_input("عدد المعالجات (Workers):", min_value=1, max_value=32, value=config_data.get('solver_workers', 8))
        
        if st.button("💾 حفظ الإعدادات", type='primary', key="save_config_btn"):
            config_data['max_solutions_to_generate'] = max_sol
            config_data['allow_empty_slots'] = allow_empty
            config_data['default_allow_same_subject_same_day'] = default_allow_same
            config_data['time_limit_seconds'] = time_lim
            config_data['empty_slot_penalty'] = empty_pen
            config_data['solver_workers'] = solver_w
            if save_config(config_data): 
                st.success("✅ تم حفظ الإعدادات بنجاح!")

    with tab_prefs:
"""
    new_lines = lines[:start_idx] + tabs_code.splitlines(True)
    
    # Indent the rest of main()
    for i in range(start_idx, len(lines)):
        line = lines[i]
        if line.startswith("if __name__ =="):
            new_lines.extend(lines[i:])
            break
        else:
            if line.strip() == "":
                new_lines.append(line)
            else:
                # Add 4 spaces
                new_lines.append("    " + line)

    with open(file_path, "w", encoding="utf-8") as f:
        f.writelines(new_lines)
    
    print("app_preferences.py refactored successfully.")

if __name__ == "__main__":
    refactor_app()
