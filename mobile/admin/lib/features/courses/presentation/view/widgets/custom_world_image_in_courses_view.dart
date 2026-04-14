import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class CustomWorldImageInCoursesView extends StatelessWidget {
  const CustomWorldImageInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Container(
      height: size.height * (isRotait ? 0.029 : 0.042),
      width: size.width * 0.045,
      decoration: BoxDecorations.boxDecorationToWorldImageInCoursesView(),
    );
  }
}
