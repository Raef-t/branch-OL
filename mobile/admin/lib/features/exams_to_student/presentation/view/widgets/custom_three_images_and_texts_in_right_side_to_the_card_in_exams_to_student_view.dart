import 'package:flutter/material.dart';
import '/core/sized_boxs/widths.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_three_images_in_right_side_to_card_in_exams_to_student_view.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_three_texts_in_right_side_to_card_in_exams_to_student_view.dart';

class CustomThreeImagesAndTextsInRightSideToTheCardInExamsToStudentView
    extends StatelessWidget {
  const CustomThreeImagesAndTextsInRightSideToTheCardInExamsToStudentView({
    super.key,
    required this.date,
    required this.course,
    required this.classRoom,
  });
  final String date, course, classRoom;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CustomThreeTextsInRightSideToCardInExamsToStudentView(
          date: date,
          classRoom: classRoom,
          course: course,
        ),
        Widths.width10(context: context),
        const CustomThreeImagesInRightSideToCardInExamsToStudentView(),
      ],
    );
  }
}
