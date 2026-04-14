import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';

class CustomLeftSideToTheCardInExamsToStudentView extends StatelessWidget {
  const CustomLeftSideToTheCardInExamsToStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    return SvgImageComponent(
      pathImage: Assets.images.checkInsideCircleImage,
      color: ColorsStyle.mediumGreenColor,
    );
  }
}
