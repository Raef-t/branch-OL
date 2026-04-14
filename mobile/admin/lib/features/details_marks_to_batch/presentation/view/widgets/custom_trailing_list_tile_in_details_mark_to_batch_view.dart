import 'package:flutter/cupertino.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomTrailingListTileInDetailsMarkToBatchView extends StatelessWidget {
  const CustomTrailingListTileInDetailsMarkToBatchView({
    super.key,
    required this.examsResultToBatchModel,
  });
  final ExamsResultToBatchModel examsResultToBatchModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        examsResultToBatchModel.isPassed == 1
            ? Assets.images.successImage.image()
            : Assets.images.failedImage.image(),
        Widths.width7(context: context),
        TextMedium12Component(
          text: (examsResultToBatchModel.mark ?? 0).toString(),
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBlackColor2,
        ),
      ],
    );
  }
}
