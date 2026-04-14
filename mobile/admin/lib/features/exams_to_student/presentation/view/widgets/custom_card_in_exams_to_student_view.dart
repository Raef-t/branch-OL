import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_contain_the_card_in_exams_to_student_view.dart';

class CustomCardInExamsToStudentView extends StatelessWidget {
  const CustomCardInExamsToStudentView({
    super.key,
    required this.subjectName,
    required this.date,
    required this.course,
    required this.classRoom,
  });
  final String subjectName, date, course, classRoom;
  @override
  Widget build(BuildContext context) {
    return Container(
      margin: OnlyPaddingWithoutChild.left18AndRight22AndBottom15(
        context: context,
      ),
      padding: OnlyPaddingWithoutChild.left17AndTop12AndBottom29AndRight39(
        context: context,
      ),
      decoration: BoxDecorations.boxDecorationToCardInExamsToStudentView(
        context: context,
      ),
      child: CustomContainTheCardInExamsToStudentView(
        subjectName: subjectName,
        date: date,
        course: course,
        classRoom: classRoom,
      ),
    );
  }
}
