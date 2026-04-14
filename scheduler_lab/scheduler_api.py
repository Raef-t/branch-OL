import json
import os
import sys
import argparse

from core.model import create_model
from core.variables import define_variables
from core.constraints import add_hard_constraints
from core.preferences import add_soft_constraints
from core.empty_slots import add_empty_slot_constraints
from core.objective import set_objective
from solver.run_solver import solve_model

def format_solution(solution, data):
    """
    Format the raw solver solution into a structured JSON dictionary.
    """
    formatted_schedule = []
    unassigned_lessons = []
    
    for key, value in solution.items():
        if isinstance(key, tuple):
            (cls_id, sub_id, day, slot_id) = key
            if value == 1:
                # Find teacher_id from data
                teacher_id = None
                for branch in data.get('branches', []):
                    for cls in branch.get('classes', []):
                        if cls['id'] == cls_id:
                            for sub in cls.get('subjects', []):
                                if sub['id'] == sub_id:
                                    teacher_ids = sub.get('teacher_ids', [])
                                    if teacher_ids:
                                        teacher_id = teacher_ids[0]
                                    break
                
                formatted_schedule.append({
                    "class_id": cls_id,
                    "subject_id": sub_id,
                    "teacher_id": teacher_id,
                    "day": day,
                    "slot_id": slot_id
                })
        
        elif isinstance(key, str) and key.startswith("unassigned_var_") and value > 0:
            parts = key.split("_")
            if len(parts) >= 4:
                unassigned_lessons.append({
                    "class_id": int(parts[2]),
                    "subject_id": int(parts[3]),
                    "count": value
                })
                
    return {
        "schedule": formatted_schedule,
        "unassigned": unassigned_lessons
    }

def run_solver_logic(data, config=None):
    """
    Core solver logic that can be called from CLI or API.
    Returns a dictionary result.
    """
    if config is None:
        config = {}

    # 3. Validate Data (Light validation)
    required_fields = ['branches', 'teachers', 'rooms', 'slots', 'days']
    for field in required_fields:
        if field not in data:
            return {"success": False, "error": f"Missing required field in data: {field}"}

    # 4. Generate Schedule
    try:
        model = create_model()
        variables = define_variables(model, data)
        
        unassigned_penalties = add_hard_constraints(model, variables, data, config)
        empty_vars, empty_penalties = add_empty_slot_constraints(model, variables, data, config)
        soft_penalties = add_soft_constraints(model, variables, data, config)
        
        all_penalties = soft_penalties + empty_penalties + unassigned_penalties
        set_objective(model, all_penalties)
        
        solutions = solve_model(model, variables, data, config)
        
        if solutions:
            all_solutions = []
            for idx, (obj_value, sol) in enumerate(solutions):
                formatted = format_solution(sol, data)
                all_solutions.append({
                    "solution_index": idx + 1,
                    "objective_value": obj_value,
                    "schedule": formatted["schedule"],
                    "unassigned": formatted["unassigned"],
                    "total_lessons": len(formatted["schedule"]),
                    "total_unassigned": sum(u["count"] for u in formatted["unassigned"]),
                })
            
            return {
                "success": True,
                "message": "Optimization completed successfully.",
                "total_solutions_found": len(solutions),
                "data": all_solutions[0],
                "all_solutions": all_solutions,
            }
        else:
            return {
                "success": False,
                "error": "INFEASIBLE_OR_OPTIMAL_NOT_FOUND",
                "message": "The system could not find a feasible schedule. Try relaxing constraints or reducing required lessons."
            }
            
    except Exception as e:
        return {
            "success": False,
            "error": "EXECUTION_ERROR",
            "message": str(e)
        }

def main():
    parser = argparse.ArgumentParser(description="Scheduler API for Laravel Integration")
    parser.add_argument("--input", type=str, required=True, help="Path to input JSON file")
    parser.add_argument("--config", type=str, required=False, help="Path to config JSON file")
    
    args = parser.parse_args()
    
    # Redirect stdout to stderr for logs during CLI run
    original_stdout = sys.stdout
    sys.stdout = sys.stderr

    # 1. Load Data
    try:
        with open(args.input, 'r', encoding='utf-8') as f:
            data = json.load(f)
    except Exception as e:
        sys.stdout = original_stdout
        print(json.dumps({"success": False, "error": f"Failed to load input JSON: {str(e)}"}, ensure_ascii=False))
        return

    # 2. Load Config
    config = {}
    if args.config and os.path.exists(args.config):
        try:
            with open(args.config, 'r', encoding='utf-8') as f:
                config = json.load(f)
        except Exception as e:
            print(f"Warning: Failed to load config JSON: {str(e)}", file=sys.stderr)
    else:
        default_config_path = os.path.join(os.path.dirname(__file__), 'config', 'default_config.json')
        if os.path.exists(default_config_path):
            try:
                with open(default_config_path, 'r', encoding='utf-8') as f:
                    config = json.load(f)
            except Exception:
                pass

    result = run_solver_logic(data, config)
    
    # Final output to stdout
    sys.stdout = original_stdout
    print(json.dumps(result, ensure_ascii=False, indent=2))

if __name__ == "__main__":
    main()

