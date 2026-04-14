import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_subject_name_inside_card_in_exams_to_student_view.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_three_images_and_texts_in_right_side_to_the_card_in_exams_to_student_view.dart';

class CustomRightSideToTheCardInExamsToStudentView extends StatelessWidget {
  const CustomRightSideToTheCardInExamsToStudentView({
    super.key,
    required this.subjectName,
    required this.date,
    required this.course,
    required this.classRoom,
  });
  final String subjectName, date, course, classRoom;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        CustomSubjectNameInsideCardInExamsToStudentView(
          subjectName: subjectName,
        ),
        Heights.height9(context: context),
        CustomThreeImagesAndTextsInRightSideToTheCardInExamsToStudentView(
          date: date,
          course: course,
          classRoom: classRoom,
        ),
      ],
    );
  }
}
