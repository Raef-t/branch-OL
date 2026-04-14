import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';

class ContainCircleQRInBottomNavigationBarComponent extends StatelessWidget {
  const ContainCircleQRInBottomNavigationBarComponent({super.key});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: SvgImageComponent(
        pathImage: Assets.images.qrImage,
        color: ColorsStyle.whiteColor,
      ),
    );
  }
}
