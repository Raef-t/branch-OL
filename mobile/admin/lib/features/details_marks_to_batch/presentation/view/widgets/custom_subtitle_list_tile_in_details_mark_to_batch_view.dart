import 'package:flutter/cupertino.dart';
import '/core/components/text_medium12_component.dart';
import '/core/styles/colors_style.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';
import '/gen/fonts.gen.dart';

class CustomSubtitleListTileInDetailsMarkToBatchView extends StatelessWidget {
  const CustomSubtitleListTileInDetailsMarkToBatchView({
    super.key,
    required this.examsResultToBatchModel,
  });
  final ExamsResultToBatchModel examsResultToBatchModel;
  @override
  Widget build(BuildContext context) {
    return TextMedium12Component(
      text: examsResultToBatchModel.date != null
          ? examsResultToBatchModel.date!.toString()
          : 'لا يوجد تاريخ',
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.greyColor,
    );
  }
}
