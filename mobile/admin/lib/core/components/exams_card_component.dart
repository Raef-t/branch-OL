import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/contain_exams_card_component.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';

class ExamsCardComponent extends StatelessWidget {
  const ExamsCardComponent({super.key, required this.examResultModel});
  final ExamResultModel examResultModel;
  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 0,
      color: ColorsStyle.whiteColor,
      shape: RoundedRectangleBorder(
        borderRadius: Circulars.circular10(context: context),
      ),
      child: ContainExamsCardComponent(examResultModel: examResultModel),
    );
  }
}
