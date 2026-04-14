import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class CustomTeacherImageInCoursesView extends StatelessWidget {
  const CustomTeacherImageInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Container(
      height: size.height * (isRotait ? 0.027 : 0.04),
      width: size.width * 0.043,
      decoration: BoxDecorations.boxDecorationToTeacherImageInCoursesView(),
    );
  }
}
