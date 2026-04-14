import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';

class TitleExamsListTileComponent extends StatelessWidget {
  const TitleExamsListTileComponent({super.key, required this.examResultModel});
  final ExamResultModel examResultModel;
  @override
  Widget build(BuildContext context) {
    return TextMedium14Component(
      text: examResultModel.subjectName ?? 'لا يوجد',
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
