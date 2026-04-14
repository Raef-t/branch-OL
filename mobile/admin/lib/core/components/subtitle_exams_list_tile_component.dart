import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class SubtitleExamsListTileComponent extends StatelessWidget {
  const SubtitleExamsListTileComponent({
    super.key,
    required this.examResultModel,
  });
  final ExamResultModel examResultModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Assets.images.dateImage.image(),
        Widths.width11(context: context),
        TextMedium12Component(
          text:
              '${examResultModel.date?.day}/${examResultModel.date?.month}/${examResultModel.date?.year}  ${examResultModel.date?.hour}:${examResultModel.date?.minute}',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.greyColor,
        ),
      ],
    );
  }
}
