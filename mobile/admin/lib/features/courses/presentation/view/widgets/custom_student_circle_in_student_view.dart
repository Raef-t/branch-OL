import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class CustomStudentCircleInStudentView extends StatelessWidget {
  const CustomStudentCircleInStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Container(
      height: size.height * (isRotait ? 0.055 : 0.08),
      width: size.width * 0.094,
      decoration: BoxDecorations.boxDecorationToStudentCircleInCoursesView(),
    );
  }
}
