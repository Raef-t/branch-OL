import 'package:flutter/material.dart';

class SelectionControllerClass extends ChangeNotifier {
  bool selectionMode = false;
  bool selectAll = false;
  final Map<int, bool> selectedStudents = {};
  //new
  List<int> get selectedIndexes =>
      selectedStudents.entries.where((e) => e.value).map((e) => e.key).toList();

  bool get hasSelection => selectedIndexes.isNotEmpty;
  //new
  void enterSelectionMode() {
    selectionMode = true;
    notifyListeners();
  }

  void exitSelectionMode() {
    selectionMode = false;
    selectAll = false;
    selectedStudents.clear();
    notifyListeners();
  }

  void toggleSelectAll({required int totalStudents}) {
    selectAll = !selectAll;
    selectedStudents.clear();
    for (int i = 0; i < totalStudents; i++) {
      selectedStudents[i] = selectAll;
    }
    notifyListeners();
  }

  void toggleStudent({required int index}) {
    selectedStudents[index] = !(selectedStudents[index] ?? false);
    notifyListeners();
  }

  bool isStudentSelected({required int index}) {
    return selectedStudents[index] ?? false;
  }
}

//notifyListeners: Any widget using Consumer<SelectionControllerClass> will rebuild and show checkboxes.
