from ortools.sat.python import cp_model

def solve_model(model, variables, data, config):
    """
    حل النموذج مع إعدادات مرنة من ملف التكوين
    """
    solver = cp_model.CpSolver()
    
    # تحميل إعدادات الحل من ملف التكوين
    solver.parameters.max_time_in_seconds = config.get("time_limit_seconds", 60)
    solver.parameters.num_search_workers = config.get("solver_workers", 8)
    
    print(f"✅ إعدادات الحل: وقت أقصى = {solver.parameters.max_time_in_seconds} ثانية")
    print(f"✅ عدد العاملين (Workers): {solver.parameters.num_search_workers}")
    
    # الحصول على عدد الحلول المطلوبة من التكوين
    max_solutions = config.get("max_solutions_to_generate", 3)
    print(f"✅ سيتم إنشاء حتى {max_solutions} حلول")
    
    solutions = []
    
    class SolutionCollector(cp_model.CpSolverSolutionCallback):
        """جامع الحلول الذي يتوقف بعد الوصول للحد الأقصى"""
        def __init__(self, variables, max_solutions):
            cp_model.CpSolverSolutionCallback.__init__(self)
            self.variables = variables
            self.max_solutions = max_solutions
            self.solutions = []
            self.solution_count = 0
        
        def on_solution_callback(self):
            """يتم استدعاؤه عند العثور على حل جديد"""
            self.solution_count += 1
            print(f"  ➤ تم العثور على حل رقم {self.solution_count}")
            
            # جمع المتغيرات (الحصص المجدولة والحصص المعلقة)
            solution = {}
            for key, var in self.variables.items():
                try:
                    val = self.Value(var)
                    if isinstance(key, tuple) and val == 1:
                        solution[key] = val
                    elif isinstance(key, str) and key.startswith("unassigned_var_") and val > 0:
                        solution[key] = val
                except Exception:
                    pass
            
            # الحصول على قيمة دالة الهدف (إن وجدت)
            try:
                objective_value = self.ObjectiveValue()
                print(f"    قيمة دالة الهدف: {objective_value}")
            except Exception as e:
                objective_value = 0
                print(f"    ⚠️ لم يتم تحديد دالة هدف: {e}")
            
            self.solutions.append((objective_value, solution))
            
            if self.solution_count >= self.max_solutions:
                print(f"    ✅ تم الوصول للحد الأقصى للحلول ({self.max_solutions})")
                self.StopSearch()
    
    # إنشاء الجامع وإعداده
    collector = SolutionCollector(variables, max_solutions)
    
    # حل النموذج
    print("⏳ بدء عملية الحل...")
    status_code = solver.Solve(model, collector)
    
    status_names = {
        cp_model.OPTIMAL: "OPTIMAL",
        cp_model.FEASIBLE: "FEASIBLE",
        cp_model.INFEASIBLE: "INFEASIBLE",
        cp_model.MODEL_INVALID: "MODEL_INVALID",
        cp_model.UNKNOWN: "UNKNOWN"
    }
    
    status_name = status_names.get(status_code, "UNKNOWN")
    print(f"\n✅ حالة الحل النهائية: {status_name}")
    print(f"✅ إجمالي الحلول المُولَّدة: {collector.solution_count}")
    
    # ترتيب الحلول حسب الهدف (من الأفضل إلى الأسوأ)
    if collector.solutions:
        collector.solutions.sort(key=lambda x: x[0])
        print(f"✅ تم ترتيب الحلول حسب جودة دالة الهدف")
    else:
        print("❌ لم يتم العثور على أي حلول")
    
    return collector.solutions