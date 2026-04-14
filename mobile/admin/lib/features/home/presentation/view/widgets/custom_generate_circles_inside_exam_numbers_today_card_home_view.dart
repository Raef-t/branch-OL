import 'package:flutter/material.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/features/home/presentation/view/widgets/custom_success_state_the_data_is_not_empty_in_home_view.dart';

class CustomGenerateCirclesInsideExamNumbersTodayCardHomeView
    extends StatelessWidget {
  const CustomGenerateCirclesInsideExamNumbersTodayCardHomeView({
    super.key,
    required this.examsModelList,
    required this.length,
  });
  final List<ExamsModel> examsModelList;
  final int length;
  @override
  Widget build(BuildContext context) {
    if (examsModelList.isEmpty) {
      return const TextSuccessStateButTheDataIsEmptyComponent(
        text: 'لا يوجد مذاكرات',
      );
    }
    return CustomSuccessStateTheDataIsNotEmptyInHomeView(
      length: length,
      examsModelList: examsModelList,
    );
  }
}
