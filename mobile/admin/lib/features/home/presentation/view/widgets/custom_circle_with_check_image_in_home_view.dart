import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';

class CustomCircleWithCheckImageInHomeView extends StatelessWidget {
  const CustomCircleWithCheckImageInHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.landscape;
    return Container(
      height: size.height * (isRotait ? 0.06 : 0.042),
      width: size.width * 0.074,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: ColorsStyle.littlePinkColor,
        image: DecorationImage(image: Assets.images.checkImage.provider()),
      ),
    );
  }
}
