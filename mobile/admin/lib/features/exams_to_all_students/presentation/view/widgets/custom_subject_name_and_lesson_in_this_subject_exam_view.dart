import 'package:flutter/material.dart';
import '/core/components/text_medium10_component.dart';
import '/core/components/text_medium14_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/gen/fonts.gen.dart';

class CustomSubjectNameAndLessonInThisSubjectExamView extends StatelessWidget {
  const CustomSubjectNameAndLessonInThisSubjectExamView({
    super.key,
    required this.examsModel,
    required this.subjectColor,
  });
  final ExamsModel examsModel;
  final Color subjectColor;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        TextMedium14Component(
          text:
              examsModel.name ??
              (examsModel
                      .batchSubjectModel
                      ?.instructorSubjectModel
                      ?.subjectModel
                      ?.subjectName ??
                  'لا يوجد مادة'),
          color: subjectColor,
        ),
        Heights.height8(context: context),
        TextMedium10Component(
          text: examsModel.examContent ?? 'لا يوجد محتوى',
          color: ColorsStyle.greyColor,
          fontFamily: FontFamily.tajawal,
        ),
      ],
    );
  }
}
