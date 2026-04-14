import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import '/core/sized_boxs/heights.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/features/home/presentation/view/widgets/custom_exam_numbers_today_text_home_view.dart';
import '/features/home/presentation/view/widgets/custom_generate_circles_inside_exam_numbers_today_card_home_view.dart';

class CustomSuccessStateForExamNumbersTodayCardInHomeView
    extends StatelessWidget {
  const CustomSuccessStateForExamNumbersTodayCardInHomeView({
    super.key,
    required this.length,
    required this.examsModelList,
  });
  final int length;
  final List<ExamsModel> examsModelList;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,

      children: [
        CustomExamNumbersTodayTextHomeView(length: length),
        Heights.height17(context: context),
        CustomGenerateCirclesInsideExamNumbersTodayCardHomeView(
          length: length,
          examsModelList: examsModelList,
        ),
      ],
    );
  }
}
