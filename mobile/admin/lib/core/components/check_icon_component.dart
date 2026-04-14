import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

class CheckIconComponent extends StatelessWidget {
  const CheckIconComponent({super.key});

  @override
  Widget build(BuildContext context) {
    double height = MediaQuery.sizeOf(context).height;
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Icon(
      Icons.check,
      size: height * (isRotait ? 0.014 : 0.028),
      color: ColorsStyle.russetColor,
    );
  }
}
