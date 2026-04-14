"""
📋 صفحة إدارة تفضيلات المدرسين - شبكة أسبوعية تفاعلية
يمكن تشغيلها بـ: streamlit run app_preferences.py
"""
import streamlit as st
import json
import os
import time
import subprocess
import platform
import threading
import queue
import copy

# ============================================
# الثوابت والتكوينات
# ============================================

# ترجمة أسماء الأيام
DAY_LABELS = {
    "sun": "الأحد",
    "mon": "الاثنين",
    "tue": "الثلاثاء",
    "wed": "الأربعاء",
    "thu": "الخميس",
    "fri": "الجمعة",
    "sat": "السبت"
}

# حالات الخلية الأربع
STATES = {
    "neutral":    {"label": "محايد",  "emoji": "⚪", "color": "#e0e0e0", "text_color": "#555",    "order": 0},
    "preferred":  {"label": "مفضّل",  "emoji": "✅", "color": "#4CAF50", "text_color": "#fff",    "order": 1},
    "avoid":      {"label": "تجنّب",  "emoji": "⚠️", "color": "#FFC107", "text_color": "#333",    "order": 2},
    "blocked":    {"label": "محظور",  "emoji": "🚫", "color": "#F44336", "text_color": "#fff",    "order": 3},
}

# ترتيب التبديل عند النقر
STATE_CYCLE = ["neutral", "preferred", "avoid", "blocked"]

# ============================================
# دوال مساعدة لتحميل/حفظ البيانات
# ============================================

def get_data_path():
    """الحصول على مسار ملف البيانات"""
    project_root = os.path.dirname(os.path.abspath(__file__))
    return os.path.join(project_root, "data", "input.json")

def load_data():
    """تحميل بيانات الجدولة"""
    data_path = get_data_path()
    try:
        with open(data_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    except FileNotFoundError:
        st.error(f"❌ لم يتم العثور على ملف البيانات: {data_path}")
        return None
    except json.JSONDecodeError as e:
        st.error(f"❌ خطأ في قراءة ملف JSON: {e}")
        return None

def save_data(data):
    """حفظ البيانات إلى الملف"""
    data_path = get_data_path()
    try:
        # نسخة احتياطية
        backup_path = data_path.replace('.json', '_backup.json')
        if os.path.exists(data_path):
            import shutil
            shutil.copy2(data_path, backup_path)

        with open(data_path, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        return True
    except Exception as e:
        st.error(f"❌ خطأ في الحفظ: {e}")
        return False

# ============================================
# دوال تشغيل المحرك (تم استعارتها من app.py)
# ============================================

def get_config_path():
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

def get_venv_python_path():
    """الحصول على المسار المطلق لبايثون في البيئة الافتراضية"""
    project_root = os.path.dirname(os.path.abspath(__file__))
    venv_path = os.path.join(project_root, "venv")
    
    if platform.system() == "Windows":
        python_executable = os.path.join(venv_path, "Scripts", "python.exe")
    else:
        python_executable = os.path.join(venv_path, "bin", "python")
    
    return python_executable, venv_path

def run_main_script_with_venv(output_queue, stop_event):
    """تشغيل main.py باستخدام البيئة الافتراضية"""
    PROJECT_ROOT = os.path.dirname(os.path.abspath(__file__))
    MAIN_SCRIPT_PATH = os.path.join(PROJECT_ROOT, "main.py")
    python_executable, _ = get_venv_python_path()
    
    try:
        env = os.environ.copy()
        env["PYTHONIOENCODING"] = "utf-8"
        env["PYTHONUTF8"] = "1"
        
        process = subprocess.Popen(
            [python_executable, MAIN_SCRIPT_PATH],
            stdout=subprocess.PIPE,
            stderr=subprocess.STDOUT,
            text=True,
            bufsize=1,
            universal_newlines=True,
            encoding='utf-8',
            errors='replace',
            env=env
        )
        
        while True:
            if stop_event.is_set():
                process.terminate()
                output_queue.put({"success": False, "error": "🛑 تم إيقاف التنفيذ يدوياً"})
                return
                
            line = process.stdout.readline()
            if not line and process.poll() is not None:
                break
            if line:
                clean_line = ''.join(char if ord(char) < 10000 or char in "✅❌⚠️🔥🚀🎉🎈✨" else '?' for char in line)
                output_queue.put({"output_line": clean_line.rstrip('\n')})
        
        process.wait()
        success = process.returncode == 0
        output_queue.put({
            "success": success,
            "completion": "✅ اكتمل توليد الجدول بنجاح!" if success else f"❌ حدث خطأ أثناء التوليد (كود: {process.returncode})"
        })
    except Exception as e:
        output_queue.put({"success": False, "error": f"❌ خطأ: {str(e)}"})

# ============================================
# دوال تحويل التفضيلات بين الصيغ
# ============================================

def prefs_to_grid(teacher, days, slots):
    """
    تحويل تفضيلات المدرس من صيغة JSON إلى مصفوفة شبكة (grid).
    المصفوفة: grid[day][slot_id] = state_name
    """
    prefs = teacher.get('preferences', {})
    preferred_days = set(prefs.get('preferred_days', []))
    avoid_days = set(prefs.get('avoid_days', []))
    preferred_slots = set(prefs.get('preferred_slots', []))
    avoid_slots = set(prefs.get('avoid_slots', []))
    blocked_slots = prefs.get('blocked_slots', {})  # {"day": [slot_ids]}

    grid = {}
    for day in days:
        grid[day] = {}
        for slot in slots:
            sid = slot['id']

            # التحقق من المحظور أولاً (الأعلى أولوية)
            if day in blocked_slots and sid in blocked_slots[day]:
                grid[day][sid] = "blocked"
            # التحقق من حالة التقاطع بين اليوم والحصة
            elif day in avoid_days:
                # إذا اليوم بالكامل مراد تجنبه
                if sid in preferred_slots:
                    grid[day][sid] = "avoid"  # تجنب اليوم يتغلب
                elif sid in avoid_slots:
                    grid[day][sid] = "avoid"
                else:
                    grid[day][sid] = "avoid"
            elif day in preferred_days:
                if sid in avoid_slots:
                    grid[day][sid] = "avoid"  # تجنب الحصة يتغلب
                elif sid in preferred_slots:
                    grid[day][sid] = "preferred"
                else:
                    grid[day][sid] = "preferred"
            else:
                # يوم محايد
                if sid in avoid_slots:
                    grid[day][sid] = "avoid"
                elif sid in preferred_slots:
                    grid[day][sid] = "preferred"
                else:
                    grid[day][sid] = "neutral"

    return grid

def grid_to_prefs(grid, days, slots):
    """
    تحويل الشبكة العكسي إلى صيغة JSON المتوافقة مع المحرك.
    هذه الطريقة تنتج أدق تمثيل ممكن بالصيغة الأصلية.
    """
    preferred_days = []
    avoid_days = []
    preferred_slots_set = set()
    avoid_slots_set = set()
    blocked_slots = {}

    slot_ids = [s['id'] for s in slots]

    for day in days:
        day_states = [grid.get(day, {}).get(sid, "neutral") for sid in slot_ids]

        # إذا كل حصص اليوم "مفضلة" أو مزيج مفضل/محايد مع أغلبية مفضلة
        preferred_count = day_states.count("preferred")
        avoid_count = day_states.count("avoid")
        blocked_count = day_states.count("blocked")

        if avoid_count > 0 and avoid_count >= preferred_count and preferred_count == 0:
            avoid_days.append(day)
        elif preferred_count > 0 and preferred_count >= avoid_count and avoid_count == 0:
            preferred_days.append(day)

        # جمع blocked_slots
        for sid in slot_ids:
            state = grid.get(day, {}).get(sid, "neutral")
            if state == "blocked":
                if day not in blocked_slots:
                    blocked_slots[day] = []
                blocked_slots[day].append(sid)

    # جمع الحصص المفضلة والمتجنبة (بالنظر عبر جميع الأيام)
    for sid in slot_ids:
        slot_states = [grid.get(day, {}).get(sid, "neutral") for day in days]
        pref_count = slot_states.count("preferred")
        avoid_count = slot_states.count("avoid")

        if pref_count > len(days) // 2:
            preferred_slots_set.add(sid)
        if avoid_count > len(days) // 2:
            avoid_slots_set.add(sid)

    result = {}
    if preferred_days:
        result['preferred_days'] = preferred_days
    if avoid_days:
        result['avoid_days'] = avoid_days
    if preferred_slots_set:
        result['preferred_slots'] = sorted(list(preferred_slots_set))
    if avoid_slots_set:
        result['avoid_slots'] = sorted(list(avoid_slots_set))
    if blocked_slots:
        result['blocked_slots'] = blocked_slots

    return result

# ============================================
# CSS مخصص
# ============================================

def inject_css():
    st.markdown("""
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');

    .stApp {
        font-family: 'Tajawal', sans-serif;
    }

    .grid-container {
        display: grid;
        gap: 4px;
        direction: rtl;
        margin: 20px 0;
    }

    .grid-header {
        background: linear-gradient(135deg, #1a237e, #283593);
        color: white;
        padding: 10px 8px;
        text-align: center;
        font-weight: 700;
        font-size: 0.85em;
        border-radius: 8px;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .grid-day-header {
        background: linear-gradient(135deg, #0d47a1, #1565c0);
        color: white;
        padding: 10px 8px;
        text-align: center;
        font-weight: 700;
        font-size: 0.9em;
        border-radius: 8px;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .legend-container {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 15px 0;
        padding: 12px 20px;
        background: #f5f5f5;
        border-radius: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9em;
        font-weight: 500;
    }

    .legend-dot {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        display: inline-block;
    }

    .teacher-card {
        background: linear-gradient(135deg, #f8f9fa, #e8eaf6);
        border: 2px solid #c5cae9;
        border-radius: 16px;
        padding: 20px;
        margin: 15px 0;
    }

    .teacher-name {
        font-size: 1.4em;
        font-weight: 700;
        color: #1a237e;
        margin-bottom: 5px;
    }

    .teacher-level {
        font-size: 0.9em;
        color: #5c6bc0;
        margin-bottom: 10px;
    }

    .stats-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin: 10px 0;
    }

    .stat-chip {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .save-success {
        background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        border: 2px solid #4CAF50;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        font-weight: 700;
        color: #2e7d32;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
    """, unsafe_allow_html=True)

# ============================================
# الواجهة الرئيسية
# ============================================

def main():
    st.set_page_config(
        page_title="تفضيلات المدرسين - الجدولة الذكية",
        page_icon="📋",
        layout="wide",
        initial_sidebar_state="expanded"
    )

    inject_css()

    # العنوان
    st.markdown("# 📋 إدارة تفضيلات المدرسين")
    st.markdown("حدد الأوقات المفضلة والمحظورة لكل مدرس بالنقر على خلايا الشبكة")
    st.markdown("---")

    # تحميل البيانات
    data = load_data()
    if data is None:
        return

    days = data.get('days', [])
    slots = data.get('slots', [])
    teachers = data.get('teachers', [])
    priority_levels = data.get('teacher_priority_levels', {})

    if not teachers:
        st.warning("⚠️ لا يوجد مدرسون في ملف البيانات")
        return

    config_data = load_config()

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
        # ====== دليل الألوان ======
        st.markdown("""
        <div class="legend-container">
            <div class="legend-item">
                <span class="legend-dot" style="background: #4CAF50;"></span>
                <span>✅ مفضّل (Soft)</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #e0e0e0;"></span>
                <span>⚪ محايد</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #FFC107;"></span>
                <span>⚠️ تجنّب (Soft)</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #F44336;"></span>
                <span>🚫 محظور (Hard)</span>
            </div>
        </div>
        """, unsafe_allow_html=True)

        # ====== اختيار المدرس ======
        teacher_names = [f"{t['name']} (المستوى {t.get('priority_level', 2)})" for t in teachers]

        col_select, col_actions = st.columns([3, 1])
        with col_select:
            selected_idx = st.selectbox(
                "اختر المدرس:",
                range(len(teachers)),
                format_func=lambda i: teacher_names[i],
                key="teacher_select"
            )

        teacher = teachers[selected_idx]
        teacher_id = teacher['id']
        level = teacher.get('priority_level', 2)
        level_info = priority_levels.get(str(level), {})
        level_name = level_info.get('name', f'المستوى {level}')

        # عرض بطاقة المدرس
        st.markdown(f"""
        <div class="teacher-card">
            <div class="teacher-name">👨‍🏫 {teacher['name']}</div>
            <div class="teacher-level">🏆 {level_name} — معامل الترجيح: {level_info.get('weight_multiplier', 1.0)}×</div>
        </div>
        """, unsafe_allow_html=True)

        # ====== تهيئة الشبكة في session_state ======
        grid_key = f"grid_{teacher_id}"

        if grid_key not in st.session_state:
            st.session_state[grid_key] = prefs_to_grid(teacher, days, slots)

        grid = st.session_state[grid_key]

        # ====== عرض الشبكة بأزرار Streamlit ======
        st.markdown("### 📅 الشبكة الأسبوعية")
        st.caption("انقر على الزر لتبديل الحالة: محايد ← مفضّل ← تجنّب ← محظور ← محايد")

        num_slots = len(slots)

        # رأس الأعمدة: عمود فارغ للأيام + أعمدة الحصص
        header_cols = st.columns([1.2] + [1] * num_slots)
        with header_cols[0]:
            st.markdown("**اليوم \\ الحصة**")
        for idx, slot in enumerate(slots):
            with header_cols[idx + 1]:
                st.markdown(f"**{slot['label']}**")

        # صفوف الأيام
        for day in days:
            row_cols = st.columns([1.2] + [1] * num_slots)

            with row_cols[0]:
                st.markdown(f"**{DAY_LABELS.get(day, day)}**")

            for idx, slot in enumerate(slots):
                sid = slot['id']
                current_state = grid.get(day, {}).get(sid, "neutral")
                state_info = STATES[current_state]

                with row_cols[idx + 1]:
                    btn_label = f"{state_info['emoji']}"
                    btn_key = f"btn_{teacher_id}_{day}_{sid}"

                    if st.button(
                        btn_label,
                        key=btn_key,
                        use_container_width=True,
                        type="primary" if current_state == "preferred" else "secondary"
                    ):
                        # تبديل إلى الحالة التالية
                        current_idx = STATE_CYCLE.index(current_state)
                        next_state = STATE_CYCLE[(current_idx + 1) % len(STATE_CYCLE)]
                        grid[day][sid] = next_state
                        st.session_state[grid_key] = grid
                        st.rerun()

        # ====== قسم تنفيذ الحل (جديد) ======
        st.markdown("---")
        st.subheader("🚀 توليد النتائج")
        st.info("بعد حفظ التفضيلات، يمكنك الضغط هنا لتوليد الجدول الزمني الجديد.")
    
        py_exec, venv_p = get_venv_python_path()
        main_p = os.path.join(os.path.dirname(os.path.abspath(__file__)), "main.py")
        is_ready = os.path.exists(py_exec) and os.path.exists(main_p)

        if not is_ready:
            st.warning("⚠️ البيئة البرمجية غير جاهزة (تحقق من وجود venv و main.py)")

        col_run, col_stop = st.columns([3, 1])
    
        if "exec_running" not in st.session_state:
            st.session_state.exec_running = False
            st.session_state.stop_ev = threading.Event()

        run_trigger = col_run.button(
            "🚀 توليد الجدول الآن (تشغيل المحرك)", 
            type="primary", 
            use_container_width=True,
            disabled=not is_ready or st.session_state.exec_running
        )
    
        if col_stop.button("⏹️ إيقاف", use_container_width=True, disabled=not st.session_state.exec_running):
            st.session_state.stop_ev.set()

        if run_trigger:
            st.session_state.exec_running = True
            st.session_state.stop_ev.clear()
        
            output_area = st.empty()
            status_area = st.empty()
        
            out_q = queue.Queue()
            t = threading.Thread(target=run_main_script_with_venv, args=(out_q, st.session_state.stop_ev))
            t.start()
        
            lines = []
            while t.is_alive() or not out_q.empty():
                while not out_q.empty():
                    item = out_q.get_nowait()
                    if "output_line" in item:
                        lines.append(item["output_line"])
                        output_area.markdown(f"```text\n" + "\n".join(lines[-15:]) + "\n```")
                    elif "error" in item:
                        status_area.error(item["error"])
                    elif "completion" in item:
                        if item.get("success"):
                            status_area.success(item["completion"])
                            st.balloons()
                            # عرض رابط لآخر ملف ملف Excel تم توليده
                            st.success("📂 تم إنشاء ملف Excel في مجلد المشروع.")
                        else:
                            status_area.error(item["completion"])
                time.sleep(0.1)
        
            st.session_state.exec_running = False

        # ====== عرض النتائج السابقة ======
        with st.expander("📂 عرض ملفات الجداول المنتجة "):
            project_root = os.path.dirname(os.path.abspath(__file__))
            files = [f for f in os.listdir(project_root) if f.startswith("timetable_output_") and f.endswith(".xlsx")]
            files.sort(reverse=True) # الأحدث أولاً
        
            if files:
                st.write("أحدث الجداول التي تم توليدها:")
                for f in files[:5]: # عرض آخر 5 ملفات
                    st.markdown(f"- 📄 `{f}`")
                st.info("💡 يمكنك العثور على هذه الملفات داخل مجلد `scheduler_lab` في جهازك.")
            else:
                st.write("لا توجد ملفات ناتجة بعد. اضغط على 'توليد الجدول' أعلاه.")

        # ====== إحصائيات سريعة ======
        st.markdown("---")
        st.markdown("### 📊 ملخص التفضيلات")

        counts = {"preferred": 0, "neutral": 0, "avoid": 0, "blocked": 0}
        for day in days:
            for slot in slots:
                state = grid.get(day, {}).get(slot['id'], "neutral")
                counts[state] += 1

        total = sum(counts.values())
        stat_cols = st.columns(4)
        stat_items = [
            ("✅ مفضّل", counts["preferred"], "#4CAF50"),
            ("⚪ محايد", counts["neutral"], "#9e9e9e"),
            ("⚠️ تجنّب", counts["avoid"], "#FFC107"),
            ("🚫 محظور", counts["blocked"], "#F44336"),
        ]
        for col, (label, count, color) in zip(stat_cols, stat_items):
            with col:
                pct = (count / total * 100) if total > 0 else 0
                st.metric(label=label, value=count, delta=f"{pct:.0f}%")

        # ====== أزرار سريعة ======
        st.markdown("---")
        st.markdown("### ⚡ إجراءات سريعة")
        quick_cols = st.columns(4)

        with quick_cols[0]:
            if st.button("🟢 تفضيل الكل", use_container_width=True):
                for day in days:
                    for slot in slots:
                        grid[day][slot['id']] = "preferred"
                st.session_state[grid_key] = grid
                st.rerun()

        with quick_cols[1]:
            if st.button("⚪ تحييد الكل", use_container_width=True):
                for day in days:
                    for slot in slots:
                        grid[day][slot['id']] = "neutral"
                st.session_state[grid_key] = grid
                st.rerun()

        with quick_cols[2]:
            if st.button("🟡 تجنّب الكل", use_container_width=True):
                for day in days:
                    for slot in slots:
                        grid[day][slot['id']] = "avoid"
                st.session_state[grid_key] = grid
                st.rerun()

        with quick_cols[3]:
            if st.button("🔄 إعادة من الملف", use_container_width=True, type="secondary"):
                st.session_state[grid_key] = prefs_to_grid(teacher, days, slots)
                st.rerun()

        # ====== حفظ التفضيلات ======
        st.markdown("---")
        save_col1, save_col2 = st.columns([3, 1])

        with save_col1:
            if st.button("💾 حفظ تفضيلات هذا المدرس", type="primary", use_container_width=True):
                # تحويل الشبكة إلى صيغة JSON
                new_prefs = grid_to_prefs(grid, days, slots)

                # تحديث بيانات المدرس
                fresh_data = load_data()
                if fresh_data:
                    for t in fresh_data['teachers']:
                        if t['id'] == teacher_id:
                            t['preferences'] = new_prefs
                            break

                    if save_data(fresh_data):
                        st.markdown('<div class="save-success">✅ تم حفظ التفضيلات بنجاح!</div>',
                                    unsafe_allow_html=True)
                        st.balloons()

        with save_col2:
            if st.button("💾 حفظ الكل", use_container_width=True):
                # حفظ تفضيلات جميع المدرسين الذين تم تعديلهم
                fresh_data = load_data()
                if fresh_data:
                    saved_count = 0
                    for t in fresh_data['teachers']:
                        t_grid_key = f"grid_{t['id']}"
                        if t_grid_key in st.session_state:
                            t_grid = st.session_state[t_grid_key]
                            t['preferences'] = grid_to_prefs(t_grid, days, slots)
                            saved_count += 1

                    if save_data(fresh_data):
                        st.markdown(f'<div class="save-success">✅ تم حفظ تفضيلات {saved_count} مدرس بنجاح!</div>',
                                    unsafe_allow_html=True)
                        st.balloons()

        # ====== معاينة JSON ======
        with st.expander("🔍 معاينة بيانات التفضيلات (JSON)"):
            preview_prefs = grid_to_prefs(grid, days, slots)
            st.json(preview_prefs)

        # ====== رابط العودة ======
        st.markdown("---")
        st.caption("📱 للعودة إلى محرر الإعدادات العامة، شغّل: `streamlit run app.py`")

if __name__ == "__main__":
    main()
