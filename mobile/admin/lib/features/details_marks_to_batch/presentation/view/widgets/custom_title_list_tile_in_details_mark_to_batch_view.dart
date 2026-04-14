import 'package:flutter/cupertino.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';

class CustomTitleListTileInDetailsMarkToBatchView extends StatelessWidget {
  const CustomTitleListTileInDetailsMarkToBatchView({
    super.key,
    required this.examsResultToBatchModel,
  });
  final ExamsResultToBatchModel examsResultToBatchModel;
  @override
  Widget build(BuildContext context) {
    return TextMedium14Component(
      text: examsResultToBatchModel.studentName ?? 'لا يوجد اسم',
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
