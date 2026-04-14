import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/courses/presentation/view/widgets/custom_teacher_image_in_courses_view.dart';
import '/features/courses/presentation/view/widgets/custom_world_image_in_courses_view.dart';

class CustomTwoImagesInsideCardInCoursesView extends StatelessWidget {
  const CustomTwoImagesInsideCardInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        const CustomTeacherImageInCoursesView(),
        Heights.height14(context: context),
        const CustomWorldImageInCoursesView(),
      ],
    );
  }
}
