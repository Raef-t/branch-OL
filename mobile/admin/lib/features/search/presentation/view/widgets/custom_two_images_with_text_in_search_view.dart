import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class CustomTwoImagesWithTextInSearchView extends StatelessWidget {
  const CustomTwoImagesWithTextInSearchView({
    super.key,
    required this.text,
    required this.onPressed,
    required this.image,
  });
  final String text;
  final void Function() onPressed;
  final Widget image;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        image,
        const Spacer(),
        TextButton(
          onPressed: onPressed,
          child: TextMedium14Component(
            text: text,
            color: ColorsStyle.greyColor,
          ),
        ),
      ],
    );
  }
}
