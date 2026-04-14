import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/gen/fonts.gen.dart';

class CustomTwoCircleAvatarsInHomeView extends StatelessWidget {
  const CustomTwoCircleAvatarsInHomeView({super.key, required this.number});
  final int number;
  @override
  Widget build(BuildContext context) {
    double height = MediaQuery.sizeOf(context).height;
    final isRotait = MediaQuery.orientationOf(context) == Orientation.landscape;
    return CircleAvatar(
      radius: height * (isRotait ? 0.03 : 0.021),
      backgroundColor: ColorsStyle.littlePinkColor,
      child: CircleAvatar(
        backgroundColor: ColorsStyle.russetColor,
        radius: height * (isRotait ? 0.028 : 0.019),
        child: Text(
          number.toString(),
          style: TextsStyle.normal14(context: context).copyWith(
            fontFamily: FontFamily.poppins,
            color: ColorsStyle.whiteColor,
          ),
        ),
      ),
    );
  }
}
