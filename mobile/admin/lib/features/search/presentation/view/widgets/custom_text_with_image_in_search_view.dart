import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';

class CustomTextWithImageInSearchView extends StatelessWidget {
  const CustomTextWithImageInSearchView({super.key, required this.text});
  final String text;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        TextMedium14Component(text: text, color: ColorsStyle.greyColor),
        Widths.width7(context: context),
        Assets.images.reloadAgainImage.image(),
      ],
    );
  }
}
