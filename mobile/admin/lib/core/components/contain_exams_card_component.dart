import 'package:flutter/material.dart';
import '/core/components/exams_list_tile_component.dart';
import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';

class ContainExamsCardComponent extends StatelessWidget {
  const ContainExamsCardComponent({super.key, required this.examResultModel});
  final ExamResultModel examResultModel;
  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: ExamsListTileComponent(examResultModel: examResultModel),
    );
  }
}
