import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class MediumCircleDotComponent extends StatelessWidget {
  const MediumCircleDotComponent({super.key, required this.color});
  final Color color;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return Container(
      height: size.height * 0.022,
      width: size.width * 0.039,
      decoration: BoxDecorations.boxDecorationToMediumCircleDotComponent(
        color: color,
      ),
    );
  }
}
