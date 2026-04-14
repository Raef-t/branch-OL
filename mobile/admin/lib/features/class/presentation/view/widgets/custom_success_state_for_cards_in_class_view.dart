import 'package:flutter/material.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';
import '/features/class/presentation/view/widgets/custom_card_about_student_in_class_view.dart';

class CustomSuccessStateForCardsInClassView extends StatelessWidget {
  const CustomSuccessStateForCardsInClassView({
    super.key,
    required this.length,
    required this.listOfBatchStudentsModel,
    required this.isVisible,
    required this.selectedIndex,
  });
  final int length;
  final List<BatchStudentsModel> listOfBatchStudentsModel;
  final bool isVisible;
  final int selectedIndex;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(length, (index) {
        final batchStudentsModel = listOfBatchStudentsModel[index];
        return CustomCardAboutStudentInClassView(
          batchStudentsModel: batchStudentsModel,
          index: index,
          isVisible: isVisible,
          selectedIndex: selectedIndex,
        );
      }),
    );
  }
}
