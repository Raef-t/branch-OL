import json
import os
import sys
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import Dict, Any, Optional

# Import the core logic directly
try:
    from scheduler_api import run_solver_logic
except ImportError:
    # Add current directory to path if not already there
    sys.path.append(os.path.dirname(os.path.abspath(__file__)))
    from scheduler_api import run_solver_logic

app = FastAPI(title="Scheduler API", description="API for generating institute schedules")

class ScheduleRequest(BaseModel):
    data: Dict[str, Any]
    config: Optional[Dict[str, Any]] = {}

@app.post("/run")
async def run_solver(request: ScheduleRequest):
    """
    Runs the scheduling solver with the provided data and configuration.
    """
    try:
        # Call the solver logic directly
        result = run_solver_logic(request.data, request.config)
        
        if result.get("success"):
            return result
        else:
            # Return 200 with success: false for predictable errors, 
            # or 400 if you prefer. Here we return 200 as the solver worked but found no solution.
            return result

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Execution error: {str(e)}")

@app.get("/health")
async def health_check():
    return {"status": "healthy", "service": "scheduler"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)

