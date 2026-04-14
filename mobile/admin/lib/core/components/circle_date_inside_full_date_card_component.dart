import 'package:flutter/material.dart';
import '/core/components/text_normal12_component.dart';
import '/core/decorations/box_decorations.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CircleDateInsideFullDateCardComponent extends StatelessWidget {
  const CircleDateInsideFullDateCardComponent({
    super.key,
    required this.circleValues,
  });
  final int circleValues;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Container(
      height: size.height * (isRotait ? 0.023 : 0.04),
      width: size.width * (isRotait ? 0.039 : 0.07),
      alignment: Alignment.center,
      decoration:
          BoxDecorations.boxDecorationToCircleDateInsideFullDateCardComponent(
            context: context,
          ),
      child: TextNormal12Component(
        text: circleValues.toString(),
        fontFamily: FontFamily.inter,
        color: ColorsStyle.mediumWhiteColor,
      ),
    );
  }
}
