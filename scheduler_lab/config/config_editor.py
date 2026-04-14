import json
import tkinter as tk
from tkinter import messagebox
import customtkinter as ctk

# تهيئة إعدادات المظهر
ctk.set_appearance_mode("System")  # "Light", "Dark", "System"
ctk.set_default_color_theme("blue")  # Themes: blue, green, dark-blue

class ConfigEditorApp(ctk.CTk):
    def __init__(self):
        super().__init__()
        
        # إعداد الخطوط العربية
        self.arabic_font = ctk.CTkFont(family="Traditional Arabic", size=16, weight="normal")
        self.arabic_bold_font = ctk.CTkFont(family="Traditional Arabic", size=18, weight="bold")
        self.arabic_title_font = ctk.CTkFont(family="Traditional Arabic", size=22, weight="bold")
        self.arabic_small_font = ctk.CTkFont(family="Traditional Arabic", size=14, weight="normal")
        
        self.title("محرر إعدادات الجدولة الذكية")
        self.geometry("1100x850")
        
        # متغيرات للربط مع الواجهة
        self.config_vars = {}
        self.create_widgets()
        self.load_config()

    def create_widgets(self):
        # إطار قابل للتمرير
        scroll_frame = ctk.CTkScrollableFrame(self)
        scroll_frame.pack(fill="both", expand=True, padx=25, pady=25)
        
        # العنوان الرئيسي - بحجم أكبر وخط عربي واضح
        title = ctk.CTkLabel(
            scroll_frame, 
            text="إعدادات نظام الجدولة الذكية", 
            font=self.arabic_title_font,
            anchor="e"
        )
        title.grid(row=0, column=0, columnspan=3, pady=(0, 25), sticky="e")
        
        row = 1
        
        # =============== القسم العام ===============
        self.create_section_header(scroll_frame, "الإعدادات العامة", row)
        row += 1
        
        # السماح بفترات فارغة
        self.create_setting(
            scroll_frame, row, "allow_empty_slots",
            "السماح بفترات فارغة",
            "هل تريد السماح بوجود فترات دراسية فارغة في الجدول؟\nتفعيل هذا الخيار يعطي مرونة أكثر لكن قد يقلل الكثافة التعليمية.",
            widget_type="checkbox"
        )
        row += 1
        
        # عقوبة الفترات الفارغة
        self.create_setting(
            scroll_frame, row, "empty_slot_penalty",
            "عقوبة الفترات الفارغة",
            "قيمة العقوبة عند وجود فترة فارغة (كلما زادت القيمة قل احتمال وجود فترات فارغة).\nالنطاق المقترح: 1-10",
            widget_type="number",
            from_=1, to=100
        )
        row += 1
        
        # الحد الأقصى للحلول
        self.create_setting(
            scroll_frame, row, "max_solutions_to_generate",
            "الحد الأقصى للحلول",
            "عدد الحلول المختلفة التي سيحاول النظام توليدها.\nزيادة العدد يحسن الجودة لكن يستغرق وقتاً أطول.",
            widget_type="number",
            from_=1, to=20
        )
        row += 1
        
        # وقت التنفيذ
        self.create_setting(
            scroll_frame, row, "time_limit_seconds",
            "الوقت المسموح (ثواني)",
            "الحد الزمني الأقصى للبحث عن حلول.\nالقيمة 0 تعني لا حد زمني (غير موصى به).",
            widget_type="number",
            from_=0, to=3600
        )
        row += 1
        
        # عدد العمليات المتوازية
        self.create_setting(
            scroll_frame, row, "solver_workers",
            "عدد العمليات المتوازية",
            "عدد الأنوية/الخيوط المستخدمة في الحساب.\nالقيمة المثلى = عدد أنوية المعالج المتاح.",
            widget_type="number",
            from_=1, to=16
        )
        row += 2
        
        # =============== أوزان تفضيلات المدرسين ===============
        self.create_section_header(scroll_frame, "أوزان تفضيلات المدرسين", row)
        row += 1
        
        preferences = [
            ("preferred_days", "التفضيل: أيام مفضلة", "تأثير اختيار أيام محددة كأيام عمل مفضلة"),
            ("avoid_days", "التفضيل: تجنب أيام", "تأثير تجنب أيام معينة (ذو أولوية أعلى من الأيام المفضلة)"),
            ("preferred_slots", "التفضيل: فترات مفضلة", "تأثير اختيار فترات زمنية محددة كأوقات مفضلة"),
            ("avoid_slots", "التفضيل: تجنب فترات", "تأثير تجنب فترات زمنية معينة (ذو أولوية أعلى من الفترات المفضلة)")
        ]
        
        for pref_key, label_text, desc in preferences:
            self.create_setting(
                scroll_frame, row, f"teacher_preference_weights.{pref_key}",
                label_text,
                desc,
                widget_type="number",
                from_=1, to=100
            )
            row += 1
        row += 1
        
        # =============== مستويات أولوية المدرسين ===============
        self.create_section_header(scroll_frame, "مستويات أولوية المدرسين", row)
        row += 1
        
        priority_desc = (
            "يتم ضرب تفضيلات المدرس في معامل المستوى\n"
            "لحساب الأولوية النهائية. المستويات:\n"
            "1: دكتوراه/خبير (معامل 1.5)\n"
            "2: ماجستير/متوسط (معامل 1.0)\n"
            "3: بكالوريوس/مبتدئ (معامل 0.7)"
        )
        
        desc_label = ctk.CTkLabel(
            scroll_frame, 
            text=priority_desc,
            wraplength=850,
            justify="right",
            font=self.arabic_small_font,
            anchor="e"
        )
        desc_label.grid(row=row, column=0, columnspan=3, sticky="e", pady=(0, 15))
        row += 1
        
        for level in ["1", "2", "3"]:
            level_frame = ctk.CTkFrame(scroll_frame)
            level_frame.grid(row=row, column=0, columnspan=3, sticky="ew", pady=8, padx=10)
            
            # اسم المستوى
            name_var = ctk.StringVar()
            self.config_vars[f"teacher_priority_levels.{level}.name"] = name_var
            
            # معامل الترجيح
            mult_var = ctk.StringVar()
            self.config_vars[f"teacher_priority_levels.{level}.weight_multiplier"] = mult_var
            
            # تنسيق الإطار للغة العربية
            level_frame.grid_columnconfigure(0, weight=1)
            level_frame.grid_columnconfigure(1, weight=0)
            level_frame.grid_columnconfigure(2, weight=0)
            level_frame.grid_columnconfigure(3, weight=0)
            
            # اسم المستوى
            name_label = ctk.CTkLabel(
                level_frame, 
                text=f"اسم المستوى {level}:",
                font=self.arabic_font,
                anchor="e"
            )
            name_label.grid(row=0, column=3, padx=10, pady=5, sticky="e")
            
            name_entry = ctk.CTkEntry(
                level_frame, 
                textvariable=name_var,
                width=250,
                font=self.arabic_font,
                justify="right"
            )
            name_entry.grid(row=0, column=2, padx=10, pady=5, sticky="e")
            
            # معامل الترجيح
            mult_label = ctk.CTkLabel(
                level_frame, 
                text="معامل الترجيح:",
                font=self.arabic_font,
                anchor="e"
            )
            mult_label.grid(row=0, column=1, padx=10, pady=5, sticky="e")
            
            mult_entry = ctk.CTkEntry(
                level_frame, 
                textvariable=mult_var,
                width=100,
                font=self.arabic_font,
                justify="right"
            )
            mult_entry.grid(row=0, column=0, padx=10, pady=5, sticky="e")
            
            row += 1
        row += 1
        
        # =============== الإعدادات الافتراضية ===============
        self.create_section_header(scroll_frame, "الإعدادات الافتراضية", row)
        row += 1
        
        self.create_setting(
            scroll_frame, row, "default_allow_same_subject_same_day",
            "نفس المادة في يوم واحد",
            "السماح بجدولة نفس المادة عدة مرات في اليوم نفسه للمدرس.\nتعطيل هذا الخيار يوزع المواد على أيام مختلفة.",
            widget_type="checkbox"
        )
        row += 2
        
        # =============== أزرار التحكم ===============
        btn_frame = ctk.CTkFrame(scroll_frame, fg_color="transparent")
        btn_frame.grid(row=row, column=0, columnspan=3, pady=30)
        
        save_btn = ctk.CTkButton(
            btn_frame, 
            text="حفظ الإعدادات", 
            command=self.save_config,
            width=220,
            height=50,
            font=ctk.CTkFont(family="Traditional Arabic", size=18, weight="bold"),
            corner_radius=15
        )
        save_btn.grid(row=0, column=1, padx=20)
        
        reset_btn = ctk.CTkButton(
            btn_frame, 
            text="إعادة الضبط",
            command=self.reset_defaults,
            width=220,
            height=50,
            font=ctk.CTkFont(family="Traditional Arabic", size=18, weight="bold"),
            fg_color="gray70",
            hover_color="gray80",
            corner_radius=15
        )
        reset_btn.grid(row=0, column=0, padx=20)

    def create_section_header(self, parent, text, row):
        header = ctk.CTkLabel(
            parent, 
            text=text, 
            font=ctk.CTkFont(family="Traditional Arabic", size=20, weight="bold"),
            anchor="e"
        )
        header.grid(row=row, column=0, columnspan=3, sticky="e", pady=(20, 8), padx=(0, 10))

    def create_setting(self, parent, row, key, label_text, description, widget_type="entry", **kwargs):
        # إطار للإعداد مع تنسيق عربي
        setting_frame = ctk.CTkFrame(parent, fg_color="transparent")
        setting_frame.grid(row=row, column=0, columnspan=3, sticky="ew", pady=8)
        setting_frame.grid_columnconfigure(0, weight=2)  # وصف
        setting_frame.grid_columnconfigure(1, weight=0)  # قيمة
        setting_frame.grid_columnconfigure(2, weight=1)  # عنوان
        
        # تسمية الإعداد
        label = ctk.CTkLabel(
            setting_frame, 
            text=label_text, 
            font=self.arabic_bold_font,
            anchor="e"
        )
        label.grid(row=0, column=2, padx=15, pady=5, sticky="e")
        
        # واجهة الإدخال
        if widget_type == "checkbox":
            var = ctk.BooleanVar()
            widget = ctk.CTkCheckBox(
                setting_frame, 
                variable=var,
                text="",
                width=20
            )
            widget.grid(row=0, column=1, padx=15, pady=5)
        elif widget_type == "number":
            var = ctk.StringVar()
            widget = ctk.CTkEntry(
                setting_frame, 
                textvariable=var,
                width=100,
                font=self.arabic_font,
                justify="center"
            )
            widget.grid(row=0, column=1, padx=15, pady=5)
            # التحقق من الإدخال الرقمي
            var.trace("w", lambda *args, v=var: self.validate_number(v))
        else:
            var = ctk.StringVar()
            widget = ctk.CTkEntry(
                setting_frame, 
                textvariable=var,
                width=300,
                font=self.arabic_font,
                justify="right"
            )
            widget.grid(row=0, column=1, padx=15, pady=5)
        
        self.config_vars[key] = var
        
        # وصف الإعداد
        desc = ctk.CTkLabel(
            setting_frame, 
            text=description, 
            wraplength=500,
            justify="right",
            font=self.arabic_font,
            anchor="e"
        )
        desc.grid(row=0, column=0, padx=15, pady=5, sticky="e")
        
        return var

    def validate_number(self, var):
        value = var.get()
        if value and not value.isdigit():
            var.set(''.join(filter(str.isdigit, value)))

    def load_config(self):
        try:
            with open('config.json', 'r', encoding='utf-8') as f:
                config = json.load(f)
        except FileNotFoundError:
            config = self.get_default_config()
            self.save_config_file(config)
        
        # تعبئة القيم في الواجهة
        for key, var in self.config_vars.items():
            value = self.get_nested_value(config, key)
            if isinstance(var, ctk.BooleanVar):
                var.set(bool(value))
            elif isinstance(var, ctk.StringVar):
                var.set(str(value))

    def get_nested_value(self, config, key):
        keys = key.split('.')
        value = config
        for k in keys:
            if isinstance(value, dict):
                value = value.get(k, "")
            else:
                return ""
        return value

    def save_config(self):
        new_config = self.get_default_config()
        
        # جمع القيم من الواجهة
        for key, var in self.config_vars.items():
            value = var.get()
            keys = key.split('.')
            
            # تحويل القيم إلى أنواعها الصحيحة
            if key in ["allow_empty_slots", "default_allow_same_subject_same_day"]:
                value = bool(value)
            elif "penalty" in key or "max_solutions" in key or "time_limit" in key or "workers" in key:
                value = int(value) if value.isdigit() else 0
            elif "weight_multiplier" in key:
                try:
                    value = float(value)
                except:
                    value = 1.0
            
            # حفظ في التوصيف المتداخل
            current = new_config
            for i, k in enumerate(keys[:-1]):
                if k not in current or not isinstance(current[k], dict):
                    current[k] = {}
                current = current[k]
            current[keys[-1]] = value
        
        self.save_config_file(new_config)
        messagebox.showinfo("نجاح", "تم حفظ الإعدادات بنجاح!")

    def save_config_file(self, config):
        with open('config.json', 'w', encoding='utf-8') as f:
            json.dump(config, f, ensure_ascii=False, indent=2)

    def reset_defaults(self):
        if messagebox.askyesno("تأكيد", "هل تريد إعادة تعيين جميع الإعدادات للقيم الافتراضية؟"):
            default_config = self.get_default_config()
            self.save_config_file(default_config)
            self.load_config()

    def get_default_config(self):
        return {
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
                "1": { "name": "دكتوراه/خبير", "weight_multiplier": 1.5 },
                "2": { "name": "ماجستير/متوسط", "weight_multiplier": 1.0 },
                "3": { "name": "بكالوريوس/مبتدئ", "weight_multiplier": 0.7 }
            },
            "default_allow_same_subject_same_day": False
        }

if __name__ == "__main__":
    app = ConfigEditorApp()
    app.mainloop()