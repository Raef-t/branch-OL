import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class CustomDotInsideCardTabBarInCoursesDetailsView extends StatelessWidget {
  const CustomDotInsideCardTabBarInCoursesDetailsView({
    super.key,
    required this.color,
  });
  final Color color;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Container(
      height: size.height * (isRotait ? 0.01 : 0.02),
      width: size.width * 0.018,
      decoration:
          BoxDecorations.boxDecorationToDotInsideCardTabBarInCoursesViewDetails(
            color: color,
          ),
    );
  }
}
