import streamlit as st
import json
import os
import time
from datetime import datetime
import subprocess
import sys
import platform
import threading
import queue
import io

# إضافة CSS مخصص
def add_custom_styles():
    st.markdown("""
    <style>
    .success-message {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-left: 5px solid #2ECC71;
        padding: 15px 20px;
        border-radius: 0 10px 10px 0;
        margin: 15px 0;
        animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .error-message {
        background: linear-gradient(135deg, #fff5f5, #ffeaea);
        border-left: 5px solid #E74C3C;
        padding: 15px 20px;
        border-radius: 0 10px 10px 0;
        margin: 15px 0;
        animation: fadeIn 0.5s ease;
    }
    .env-info {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        border-left: 5px solid #2196F3;
        padding: 15px 20px;
        border-radius: 0 10px 10px 0;
        margin: 15px 0;
        animation: fadeIn 0.5s ease;
    }
    .saving-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        background-color: #3498DB;
        border-radius: 50%;
        margin: 0 5px;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.4; transform: scale(0.8); }
        50% { opacity: 1; transform: scale(1); }
        100% { opacity: 0.4; transform: scale(0.8); }
    }
    .execution-output {
        background-color: #2d2d2d;
        color: #f8f8f2;
        font-family: 'Courier New', monospace;
        padding: 15px;
        border-radius: 10px;
        font-size: 0.95em;
        max-height: 400px;
        overflow-y: auto;
        white-space: pre-wrap;
        direction: ltr;
        margin: 10px 0;
    }
    .execution-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background: linear-gradient(135deg, #3498DB, #2980B9);
        color: white;
        border-radius: 10px 10px 0 0;
        margin-bottom: 10px;
    }
    .guide-box {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border: 2px solid #3498DB;
        border-radius: 15px;
        padding: 20px;
        margin: 15px 0;
    }
    .config-viewer {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        font-family: monospace;
        direction: ltr;
        max-height: 300px;
        overflow-y: auto;
    }
    </style>
    """, unsafe_allow_html=True)

# دالة الحصول على مسار بايثون في البيئة الافتراضية
def get_venv_python_path():
    """الحصول على المسار المطلق لبايثون في البيئة الافتراضية"""
    project_root = os.path.dirname(os.path.abspath(__file__))
    venv_path = os.path.join(project_root, "venv")
    
    # تحديد نظام التشغيل
    if platform.system() == "Windows":
        python_executable = os.path.join(venv_path, "Scripts", "python.exe")
    else:  # Linux/Mac
        python_executable = os.path.join(venv_path, "bin", "python")
    
    return python_executable, venv_path

# دالة تشغيل main.py مع معالجة الترميز (الدالة الصحيحة الوحيدة)
def run_main_script_with_venv(output_queue, stop_event):
    """تشغيل main.py باستخدام البيئة الافتراضية مع معالجة الترميز"""
    PROJECT_ROOT = os.path.dirname(os.path.abspath(__file__))
    MAIN_SCRIPT_PATH = os.path.join(PROJECT_ROOT, "main.py")
    
    # الحصول على مسار بايثون في البيئة
    python_executable, venv_path = get_venv_python_path()
    
    # التحقق من وجود الملفات المطلوبة
    if not os.path.exists(MAIN_SCRIPT_PATH):
        output_queue.put({
            "success": False,
            "error": f"❌ لم يتم العثور على ملف main.py في: {MAIN_SCRIPT_PATH}",
            "error_type": "missing_file"
        })
        return
    
    if not os.path.exists(python_executable):
        output_queue.put({
            "success": False,
            "error": f"❌ لم يتم العثور على بايثون البيئة الافتراضية في: {python_executable}\n"
                     f"المسار المتوقع للبيئة: {venv_path}",
            "error_type": "missing_venv"
        })
        return
    
    try:
        output_queue.put({
            "info": f"⚙️ سيتم استخدام بايثون البيئة من: {python_executable}"
        })
        
        # تحديد متغيرات البيئة لحل مشكلة الترميز
        env = os.environ.copy()
        env["PYTHONIOENCODING"] = "utf-8"
        env["PYTHONUTF8"] = "1"  # تفعيل دعم UTF-8 الكامل
        
        # تشغيل البرنامج مع ترميز UTF-8
        process = subprocess.Popen(
            [python_executable, MAIN_SCRIPT_PATH],
            stdout=subprocess.PIPE,
            stderr=subprocess.STDOUT,
            text=True,
            bufsize=1,
            universal_newlines=True,
            encoding='utf-8',  # تحديد الترميز
            errors='replace',  # استبدال الأحرف غير القابلة للتحويل
            env=env
        )
        
        # قراءة المخرجات في الوقت الفعلي
        while True:
            if stop_event.is_set():
                process.terminate()
                output_queue.put({
                    "success": False,
                    "error": "🛑 تم إيقاف التنفيذ يدوياً",
                    "error_type": "stopped_manually"
                })
                return
                
            line = process.stdout.readline()
            if not line and process.poll() is not None:
                break
            if line:
                # تنظيف الأحرف غير الآمنة
                clean_line = ''.join(
                    char if ord(char) < 10000 or char in "✅❌⚠️🔥🚀🎉🎈✨" 
                    else '?' for char in line
                )
                output_queue.put({
                    "output_line": clean_line.rstrip('\n')
                })
        
        # انتظار اكتمال العملية
        process.wait()
        
        return_code = process.returncode
        success = return_code == 0
        
        output_queue.put({
            "success": success,
            "return_code": return_code,
            "completion": "✅ التنفيذ اكتمل بنجاح!" if success else f"❌ التنفيذ اكتمل مع كود حالة: {return_code}"
        })
        
    except Exception as e:
        output_queue.put({
            "success": False,
            "error": f"❌ خطأ أثناء التشغيل: {str(e)}\n"
                     f"مسار بايثون: {python_executable}\n"
                     f"مسار main.py: {MAIN_SCRIPT_PATH}",
            "error_type": "execution_error"
        })

# دالة آمنة لتحميل الإعدادات
def load_config():
    """تحميل الإعدادات مع معالجة الأخطاء"""
    # المسار الصحيح للملف
    project_root = os.path.dirname(os.path.abspath(__file__))
    config_dir = os.path.join(project_root, "config")
    config_path = os.path.join(config_dir, "default_config.json")
    
    default_config = {
        "allow_empty_slots": True,
        "empty_slot_penalty": 2,
        "max_solutions_to_generate": 2,
        "time_limit_seconds": 60,
        "solver_workers": 4,
        "teacher_preference_weights": {
            "preferred_days": 10,
            "avoid_days": 20,
            "preferred_slots": 5,
            "avoid_slots": 10
        },
        "teacher_priority_levels": {
            "1": {"name": "دكتوراه/خبير", "weight_multiplier": 1.5},
            "2": {"name": "ماجستير/متوسط", "weight_multiplier": 1.0},
            "3": {"name": "بكالوريوس/مبتدئ", "weight_multiplier": 0.7}
        },
        "default_allow_same_subject_same_day": False
    }
    
    try:
        # إنشاء مجلد config إذا لم يكن موجوداً
        os.makedirs(config_dir, exist_ok=True)
        
        # التحقق إذا الملف موجود وله محتوى
        if os.path.exists(config_path) and os.path.getsize(config_path) > 0:
            with open(config_path, 'r', encoding='utf-8') as f:
                config = json.load(f)
                # التحقق من أن config قاموس وليس مجموعة
                if isinstance(config, dict):
                    return config, config_path
        else:
            # إنشاء ملف الإعدادات الافتراضي إذا لم يوجد
            with open(config_path, 'w', encoding='utf-8') as f:
                json.dump(default_config, f, ensure_ascii=False, indent=2)
            return default_config, config_path
            
    except Exception as e:
        st.error(f"❌ خطأ في تحميل الإعدادات: {str(e)}")
        st.warning("⚠️ سيتم استخدام الإعدادات الافتراضية")
        return default_config, config_path

# دالة آمنة لحفظ الإعدادات
def save_config(config, config_path):
    """حفظ الإعدادات مع معالجة الأخطاء"""
    try:
        # التحقق من أن config قاموس
        if not isinstance(config, dict):
            st.error("❌ خطأ: البيانات المراد حفظها ليست قاموساً")
            return False
            
        # إنشاء نسخة احتياطية إذا كان الملف موجوداً
        if os.path.exists(config_path):
            backup_path = config_path.replace('.json', '_backup.json')
            import shutil
            shutil.copy2(config_path, backup_path)
        
        # حفظ الملف الجديد
        with open(config_path, 'w', encoding='utf-8') as f:
            json.dump(config, f, ensure_ascii=False, indent=2)
        
        st.success(f"✅ تم حفظ الإعدادات بنجاح في: {config_path}")
        return True
    except Exception as e:
        st.error(f"❌ خطأ في الحفظ إلى {config_path}: {str(e)}")
        return False

# الإعدادات الأساسية
st.set_page_config(
    page_title="محرر إعدادات الجدولة",
    page_icon="📱",
    layout="wide",
    initial_sidebar_state="expanded"
)

# إضافة الأنماط المخصصة
add_custom_styles()

st.title("📱 محرر إعدادات الجدولة")
st.info("📋 **لإدارة تفضيلات المدرسين بالشبكة التفاعلية:** شغّل `streamlit run app_preferences.py`")
st.markdown("---")

# === تحميل الإعدادات ===
config, config_path = load_config()

# === عرض معلومات البيئة الافتراضية ===
st.subheader("🔍 معلومات البيئة الافتراضية")

# الحصول على مسارات البيئة
python_executable, venv_path = get_venv_python_path()
project_root = os.path.dirname(os.path.abspath(__file__))
main_script_path = os.path.join(project_root, "main.py")

# التحقق من وجود البيئة
venv_exists = os.path.exists(venv_path)
python_exists = os.path.exists(python_executable)
main_exists = os.path.exists(main_script_path)

if venv_exists and python_exists:
    st.markdown(f'<div class="env-info">'
                f'<strong>✅ البيئة الافتراضية جاهزة!</strong><br>'
                f'مسار البيئة: <code>{venv_path}</code><br>'
                f'مسار بايثون: <code>{python_executable}</code><br>'
                f'حالة main.py: {"✅ موجود" if main_exists else "❌ غير موجود"}'
                '</div>', unsafe_allow_html=True)
else:
    st.markdown('<div class="error-message">'
                '<strong>❌ مشكلة في البيئة الافتراضية!</strong><br>'
                'لم يتم العثور على البيئة الافتراضية في المسار المتوقع.'
                '</div>', unsafe_allow_html=True)
    
    st.markdown('<div class="guide-box">', unsafe_allow_html=True)
    st.markdown("### 📋 المسار المتوقع للبيئة:")
    st.code(venv_path, language='bash')
    
    st.markdown("### 🔍 كيفية الإصلاح:")
    st.markdown("1. تأكد من وجود مجلد `venv` في: `C:\\Users\\jt-r\\Desktop\\scheduler_lab`")
    st.markdown("2. إذا كان المجلد باسم مختلف (مثل `env`)، يمكنك:")
    col1, col2 = st.columns(2)
    with col1:
        st.markdown("#### الخيار 1: إعادة تسمية المجلد")
        st.code("ren old_env_name venv", language='bash')
    with col2:
        st.markdown("#### الخيار 2: تعديل المسار في الكود")
        st.code("venv_path = os.path.join(project_root, 'اسم_مجلد_البيئة_الفعلي')", language='python')
    st.markdown('</div>', unsafe_allow_html=True)

# زر تشغيل main.py
st.markdown("---")
st.subheader("🚀 تشغيل البرنامج الرئيسي")

# تفعيل الزر فقط إذا كانت البيئة جاهزة
is_ready = venv_exists and python_exists and main_exists

if not is_ready:
    st.warning("⚠️ لا يمكن التشغيل حتى يتم حل مشاكل البيئة أعلاه.")
    
run_button = st.button(
    "▶️ تشغيل main.py", 
    type="primary", 
    use_container_width=True, 
    key="run_main",
    disabled=not is_ready
)

stop_button = st.button("⏹️ إيقاف التنفيذ", type="secondary", use_container_width=True, key="stop_main", disabled=not st.session_state.get("execution_running", False))

# حالة التنفيذ
if "execution_running" not in st.session_state:
    st.session_state.execution_running = False
    st.session_state.stop_event = threading.Event()

# منطقة عرض المخرجات
if run_button and not st.session_state.execution_running:
    st.session_state.execution_running = True
    st.session_state.stop_event.clear()
    
    st.markdown("---")
    st.subheader("📊 مخرجات التنفيذ")
    
    # إنشاء حاويات للإخراج
    exec_header = st.empty()
    output_container = st.empty()
    final_status = st.empty()
    
    # عرض رأس التنفيذ
    with exec_header.container():
        st.markdown('<div class="execution-header">'
                    '<strong>جاري تنفيذ main.py</strong>'
                    '<span class="saving-indicator"></span>'
                    '</div>', unsafe_allow_html=True)
    
    # عرض المخرجات الأولية
    with output_container.container():
        st.markdown('<div class="execution-output">جاري البدء بالتنفيذ...</div>', unsafe_allow_html=True)
    
    # إنشاء قائمة انتظار وحدث لإيقاف التنفيذ
    output_queue = queue.Queue()
    
    # تشغيل التنفيذ في خيط منفصل
    execution_thread = threading.Thread(
        target=run_main_script_with_venv,
        args=(output_queue, st.session_state.stop_event)
    )
    execution_thread.start()
    
    # مراقبة المخرجات
    accumulated_output = []
    
    while execution_thread.is_alive() or not output_queue.empty():
        try:
            # السماح لإيقاف التنفيذ
            if stop_button:
                st.session_state.stop_event.set()
                st.info("🔄 جاري إيقاف التنفيذ...")
            
            # الحصول على العناصر من القائمة
            while not output_queue.empty():
                item = output_queue.get_nowait()
                
                if "output_line" in item:
                    accumulated_output.append(item["output_line"])
                    # تحديث العرض كل عدة أسطر
                    if len(accumulated_output) % 5 == 0 or len(accumulated_output) < 10:
                        display_text = "\n".join(accumulated_output[-100:])  # عرض آخر 100 سطر
                        with output_container.container():
                            st.markdown(f'<div class="execution-output">{display_text}</div>', unsafe_allow_html=True)
                
                elif "info" in item:
                    st.info(item["info"])
                
                elif "error" in item:
                    with final_status.container():
                        st.markdown(f'<div class="error-message">{item["error"]}</div>', unsafe_allow_html=True)
                
                elif "completion" in item:
                    with final_status.container():
                        status_color = "2ECC71" if item.get("success", False) else "E74C3C"
                        st.markdown(f'<div style="color: #{status_color}; font-weight: bold; margin: 10px 0;">{item["completion"]}</div>', unsafe_allow_html=True)
                    
                    if item.get("success", False):
                        st.balloons()
            
            time.sleep(0.1)  # تأخير قصير لتقليل استخدام المعالج
        
        except Exception as e:
            st.error(f"خطأ في معالجة المخرجات: {str(e)}")
            break
    
    # انتظار اكتمال الخيط
    execution_thread.join(timeout=1.0)
    
    # تحديث حالة التنفيذ
    st.session_state.execution_running = False
    
    # عرض المخرجات المتبقية
    if accumulated_output:
        display_text = "\n".join(accumulated_output[-100:])
        with output_container.container():
            st.markdown(f'<div class="execution-output">{display_text}</div>', unsafe_allow_html=True)

# === باقي الإعدادات ===
st.markdown("---")
st.subheader("⚙️ الإعدادات العامة")
col1, col2 = st.columns(2)

with col1:
    allow_empty = st.toggle(
        "السماح بفترات فارغة",
        value=bool(config.get("allow_empty_slots", True)),
        help="هل تريد السماح بوجود فترات دراسية فارغة في الجدول؟"
    )
    
    penalty = st.number_input(
        "عقوبة الفترات الفارغة",
        min_value=1, max_value=100,
        value=int(config.get("empty_slot_penalty", 2)),
        help="كلما زادت القيمة قل احتمال وجود فترات فارغة"
    )

with col2:
    max_solutions = st.number_input(
        "الحد الأقصى للحلول",
        min_value=1, max_value=20,
        value=int(config.get("max_solutions_to_generate", 2)),
        help="عدد الحلول التي سيحاول النظام توليدها"
    )
    
    time_limit = st.number_input(
        "الوقت المسموح (ثواني)",
        min_value=0, max_value=3600,
        value=int(config.get("time_limit_seconds", 60)),
        help="الحد الزمني للبحث عن حلول"
    )

# القسم 2: أوزان التفضيلات
st.markdown("---")
st.subheader("📊 أوزان تفضيلات المدرسين")

teacher_prefs = config.get("teacher_preference_weights", {})
col3, col4 = st.columns(2)

with col3:
    preferred_days = st.number_input(
        "أيام مفضلة", 
        1, 100, 
        int(teacher_prefs.get("preferred_days", 10))
    )
    preferred_slots = st.number_input(
        "فترات مفضلة", 
        1, 100, 
        int(teacher_prefs.get("preferred_slots", 5))
    )

with col4:
    avoid_days = st.number_input(
        "تجنب أيام", 
        1, 100, 
        int(teacher_prefs.get("avoid_days", 20))
    )
    avoid_slots = st.number_input(
        "تجنب فترات", 
        1, 100, 
        int(teacher_prefs.get("avoid_slots", 10))
    )

# القسم 3: مستويات الأولوية
st.markdown("---")
st.subheader("🏆 مستويات أولوية المدرسين")

priority_levels = config.get("teacher_priority_levels", {})
for level in ["1", "2", "3"]:
    level_data = priority_levels.get(level, {})
    with st.expander(f"المستوى {level}: {level_data.get('name', 'غير معروف')}"):
        col_a, col_b = st.columns(2)
        
        with col_a:
            level_name = st.text_input(
                "اسم المستوى",
                value=level_data.get("name", f"المستوى {level}"),
                key=f"name_{level}"
            )
        
        with col_b:
            weight_mult = st.number_input(
                "معامل الترجيح",
                min_value=0.1, max_value=2.0, step=0.1,
                value=float(level_data.get("weight_multiplier", 1.0)),
                key=f"weight_{level}"
            )
        
        if level not in priority_levels:
            priority_levels[level] = {}
        priority_levels[level]["name"] = level_name
        priority_levels[level]["weight_multiplier"] = weight_mult

# القسم 4: الإعدادات الافتراضية
st.markdown("---")
st.subheader("🔧 الإعدادات الافتراضية")

same_subject = st.toggle(
    "السماح بنفس المادة في يوم واحد",
    value=bool(config.get("default_allow_same_subject_same_day", False)),
    help="هل تريد السماح بجدولة نفس المادة عدة مرات في اليوم نفسه للمدرس؟"
)

# زر الحفظ مع المؤثرات
st.markdown("---")
col1, col2 = st.columns([3, 1])

with col1:
    if st.button("💾 حفظ جميع الإعدادات", type="primary", use_container_width=True):
        config["allow_empty_slots"] = allow_empty
        config["empty_slot_penalty"] = penalty
        config["max_solutions_to_generate"] = max_solutions
        config["time_limit_seconds"] = time_limit
        config["default_allow_same_subject_same_day"] = same_subject
        
        config["teacher_preference_weights"] = {
            "preferred_days": preferred_days,
            "avoid_days": avoid_days,
            "preferred_slots": preferred_slots,
            "avoid_slots": avoid_slots
        }
        
        config["teacher_priority_levels"] = priority_levels
        
        if save_config(config, config_path):
            st.success("🎉 تم حفظ جميع الإعدادات بنجاح!")
            st.balloons()
            # إعادة تحميل الإعدادات
            config, config_path = load_config()
            st.experimental_rerun()

with col2:
    if st.button("🔄 إعادة التعيين", use_container_width=True):
        default_config, _ = load_config()
        if save_config(default_config, config_path):
            st.success("✅ تم إعادة التعيين للقيم الافتراضية!")
            st.experimental_rerun()

# === عرض محتويات ملف الإعدادات ===
st.markdown("---")
st.subheader("📋 محتويات ملف الإعدادات الحالي")

with st.expander("عرض/إخفاء محتويات الملف"):
    # قراءة محتويات الملف للعرض
    try:
        with open(config_path, 'r', encoding='utf-8') as f:
            file_content = f.read()
        
        st.markdown('<div class="config-viewer">', unsafe_allow_html=True)
        st.code(file_content, language='json')
        st.markdown('</div>', unsafe_allow_html=True)
        
        st.caption(f"📁 المسار: {config_path}")
    
    except Exception as e:
        st.error(f"❌ خطأ في قراءة ملف الإعدادات: {str(e)}")

# معلومات إضافية في الأسفل
st.markdown("---")
st.caption(f"🏠 مجلد المشروع: `{project_root}`")
st.caption(f"📁 مسار البيئة الافتراضية: `{venv_path}`")
st.caption(f"🐍 مسار بايثون المستخدم: `{python_executable}`")
st.caption(f"🎬 مسار main.py: `{main_script_path}`")
st.caption(f"💻 نظام التشغيل: {platform.system()} {platform.release()}")
st.caption(f"📄 مسار ملف الإعدادات: `{config_path}`")

# زر لفحص وجود الملفات
with st.expander("🔍 فحص حالة الملفات"):
    st.markdown("### حالة الملفات الأساسية:")
    
    files_to_check = [
        ("main.py", main_script_path),
        ("venv\\Scripts\\python.exe", python_executable),
        ("config\\default_config.json", config_path)
    ]
    
    for file_name, file_path in files_to_check:
        status = "✅ موجود" if os.path.exists(file_path) else "❌ غير موجود"
        color = "#2ECC71" if os.path.exists(file_path) else "#E74C3C"
        st.markdown(f"<span style='color: {color}; font-weight: bold;'>{file_name}:</span> {status}<br>"
                   f"<span style='color: #7f8c8d; font-size: 0.9em;'>{file_path}</span>", 
                   unsafe_allow_html=True)