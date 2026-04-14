import 'package:flutter/material.dart';
import '/core/components/text_semi_bold10_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class RightSideInsideMenuCardComponent extends StatelessWidget {
  const RightSideInsideMenuCardComponent({
    super.key,
    required this.startTime,
    required this.endTime,
  });
  final String startTime, endTime;
  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Heights.height7half(context: context),
        FittedBox(
          child: TextSemiBold10Component(
            text: startTime,
            fontFamily: FontFamily.poppins,
            color: ColorsStyle.blackColor,
          ),
        ),
        Heights.height13(context: context),
        FittedBox(
          child: TextSemiBold10Component(
            text: endTime,
            fontFamily: FontFamily.poppins,
            color: ColorsStyle.blackColor,
          ),
        ),
        Heights.height7half(context: context),
      ],
    );
  }
}
