import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/components/text_medium15_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';

class CustomTextWithDropIconInDetailsStudentView extends StatelessWidget {
  const CustomTextWithDropIconInDetailsStudentView({
    super.key,
    required this.text,
  });
  final String text;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        TextMedium15Component(text: text),
        Widths.width12(context: context),
        SvgImageComponent(
          pathImage: Assets.images.bottomArrowImage,
          color: ColorsStyle.greyColor,
        ),
      ],
    );
  }
}
