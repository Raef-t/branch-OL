import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/paddings/padding_without_child/symmetric_padding_without_child.dart';
import '/features/courses/presentation/view/widgets/custom_contain_big_circle_in_courses_view.dart';

class CustomBigCircleInCoursesView extends StatelessWidget {
  const CustomBigCircleInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Container(
      padding: isRotait
          ? OnlyPaddingWithoutChild.left33AndRight32AndTop45AndBottom45(
              context: context,
            )
          : SymmetricPaddingWithoutChild.horizontal35AndVertical45(
              context: context,
            ),
      decoration: BoxDecorations.boxDecorationToBigCircleInCoursesView(),
      child: const CustomContainBigCircleInCoursesView(),
    );
  }
}
