import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_subject_name_and_lesson_in_this_subject_exam_view.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_two_texts_and_images_details_about_exam_in_exam_view.dart';

class CustomRightSideInExamCardInExamView extends StatelessWidget {
  const CustomRightSideInExamCardInExamView({
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
        CustomSubjectNameAndLessonInThisSubjectExamView(
          examsModel: examsModel,
          subjectColor: subjectColor,
        ),
        Heights.height10(context: context),
        CustomTwoTextsAndImagesDetailsAboutExamInExamView(
          examsModel: examsModel,
        ),
      ],
    );
  }
}
