def set_objective(model, penalties):
    """
    تحديد دالة الهدف: تقليل مجموع جميع العقوبات
    """
    if penalties:
        model.Minimize(sum(penalties))
        print(f"✅ دالة الهدف: تقليل مجموع {len(penalties)} عقوبة")
    else:
        print("⚠️ لم يتم تحديد أي عقوبات - سيتم البحث عن أي حل ممكن")