import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

class DividerWithFixedSizeComponent extends StatelessWidget {
  const DividerWithFixedSizeComponent({super.key});

  @override
  Widget build(BuildContext context) {
    double width = MediaQuery.sizeOf(context).width;
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return SizedBox(
      width: width * (isRotait ? 0.183 : 0.125),
      child: const Divider(color: ColorsStyle.veryLittleGreyColor, height: 0),
    );
  }
}
