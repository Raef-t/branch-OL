import 'package:flutter/material.dart';
import '/core/components/text_medium10_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomTwoTextsAndImagesDetailsAboutExamInExamView
    extends StatelessWidget {
  const CustomTwoTextsAndImagesDetailsAboutExamInExamView({
    super.key,
    required this.examsModel,
  });
  final ExamsModel examsModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.end,
          children: [
            TextMedium10Component(
              text:
                  examsModel.batchSubjectModel?.batchModel?.course ??
                  'لا يوجد دورة',
              color: ColorsStyle.mediumBrownColor,
              fontFamily: FontFamily.tajawal,
            ),
            Widths.width10(context: context),
            Assets.images.likeStarInsideCircleImage.image(),
          ],
        ),
        Heights.height5(context: context),
        Row(
          mainAxisAlignment: MainAxisAlignment.end,
          children: [
            TextMedium10Component(
              text:
                  examsModel.batchSubjectModel?.classRoomModel?.classRoom ??
                  'لا يوجد قاعه',
              color: ColorsStyle.mediumBrownColor,
              fontFamily: FontFamily.tajawal,
            ),
            Widths.width10(context: context),
            Assets.images.locationUpCircleDeterminedImage.image(),
          ],
        ),
      ],
    );
  }
}
