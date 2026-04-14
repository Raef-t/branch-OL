import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/gen/fonts.gen.dart';

class CustomTwoTimeTextsInTimeCardExamView extends StatelessWidget {
  const CustomTwoTimeTextsInTimeCardExamView({
    super.key,
    required this.examsModel,
    required this.index,
  });
  final ExamsModel examsModel;
  final int index;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        index == 0
            ? Heights.height24(context: context)
            : Heights.height10(context: context),
        FittedBox(
          child: TextMedium12Component(
            text: examsModel.firstTime ?? '05:00',
            color: ColorsStyle.mediumBlackColor2,
            fontFamily: FontFamily.tajawal,
          ),
        ),
        Heights.height16(context: context),
        const FittedBox(
          child: TextMedium12Component(
            text: '10:00',
            color: ColorsStyle.greyColor,
            fontFamily: FontFamily.tajawal,
          ),
        ),
      ],
    );
  }
}
