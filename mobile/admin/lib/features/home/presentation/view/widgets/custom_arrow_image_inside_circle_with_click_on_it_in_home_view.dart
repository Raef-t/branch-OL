import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';

class CustomArrowImageInsideCircleWithClickOnItInHomeView
    extends StatelessWidget {
  const CustomArrowImageInsideCircleWithClickOnItInHomeView({
    super.key,
    required this.onTap,
    required this.pathImage,
    required this.color,
  });
  final void Function() onTap;
  final String pathImage;
  final Color color;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: SvgImageComponent(pathImage: pathImage, color: color),
    );
  }
}
