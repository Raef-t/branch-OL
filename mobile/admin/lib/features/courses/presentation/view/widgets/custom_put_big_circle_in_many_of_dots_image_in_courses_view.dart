import 'package:flutter/material.dart';
import '/features/courses/presentation/view/widgets/custom_big_circle_in_courses_view.dart';
import '/gen/assets.gen.dart';

class CustomPutBigCircleInManyOfDotsImageInCoursesView extends StatelessWidget {
  const CustomPutBigCircleInManyOfDotsImageInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return Stack(
      alignment: Alignment.center,
      children: [
        Assets.images.manyOfDotsOnCircleShapeImage.image(),
        const CustomBigCircleInCoursesView(),
      ],
    );
  }
}
