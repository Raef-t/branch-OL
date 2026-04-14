import 'package:flutter/material.dart';
import '/core/components/subtitle_exams_list_tile_component.dart';
import '/core/components/title_exams_list_tile_component.dart';
import '/core/components/trailing_exams_list_tile_component.dart';
import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';

class ExamsListTileComponent extends StatelessWidget {
  const ExamsListTileComponent({super.key, required this.examResultModel});
  final ExamResultModel examResultModel;
  @override
  Widget build(BuildContext context) {
    return ListTile(
      title: TitleExamsListTileComponent(examResultModel: examResultModel),
      subtitle: SubtitleExamsListTileComponent(
        examResultModel: examResultModel,
      ),
      trailing: TrailingExamsListTileComponent(
        examResultModel: examResultModel,
      ),
    );
  }
}
