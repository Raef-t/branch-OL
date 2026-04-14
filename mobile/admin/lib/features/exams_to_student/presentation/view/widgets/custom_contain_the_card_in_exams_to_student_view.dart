import 'package:flutter/material.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_left_side_to_the_card_in_exams_to_student_view.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_right_side_to_the_card_in_exams_to_student_view.dart';

class CustomContainTheCardInExamsToStudentView extends StatelessWidget {
  const CustomContainTheCardInExamsToStudentView({
    super.key,
    required this.subjectName,
    required this.date,
    required this.course,
    required this.classRoom,
  });
  final String subjectName, date, course, classRoom;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        const CustomLeftSideToTheCardInExamsToStudentView(),
        const Spacer(),
        CustomRightSideToTheCardInExamsToStudentView(
          subjectName: subjectName,
          date: date,
          course: course,
          classRoom: classRoom,
        ),
      ],
    );
  }
}
