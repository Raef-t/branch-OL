import 'package:flutter/material.dart';
import '/core/components/text_medium10_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/gen/fonts.gen.dart';

class CustomTwoTextsDetailsAboutExamInExamView extends StatelessWidget {
  const CustomTwoTextsDetailsAboutExamInExamView({
    super.key,
    required this.examsModel,
  });
  final ExamsModel examsModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        TextMedium10Component(
          text:
              examsModel.batchSubjectModel?.batchModel?.course ??
              'لا يوجد دورة',
          color: ColorsStyle.mediumBrownColor,
          fontFamily: FontFamily.tajawal,
        ),
        Heights.height5(context: context),
        TextMedium10Component(
          text:
              examsModel.batchSubjectModel?.classRoomModel?.classRoom ??
              'لا يوجد قاعه',
          color: ColorsStyle.mediumBrownColor,
          fontFamily: FontFamily.tajawal,
        ),
      ],
    );
  }
}
