import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class TrailingExamsListTileComponent extends StatelessWidget {
  const TrailingExamsListTileComponent({
    super.key,
    required this.examResultModel,
  });
  final ExamResultModel examResultModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        examResultModel.isPassed == 1
            ? Assets.images.successImage.image()
            : Assets.images.failedImage.image(),
        Widths.width7(context: context),
        TextMedium12Component(
          text: examResultModel.mark ?? 'لا يزجد',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBlackColor2,
        ),
      ],
    );
  }
}
