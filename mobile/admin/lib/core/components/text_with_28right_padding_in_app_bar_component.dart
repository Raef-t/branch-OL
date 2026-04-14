import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/styles/colors_style.dart';

class TextWith28RightPaddingInAppBarComponent extends StatelessWidget {
  const TextWith28RightPaddingInAppBarComponent({
    super.key,
    required this.text,
  });
  final String text;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right28(
      context: context,
      child: TextMedium14Component(text: text, color: ColorsStyle.greyColor),
    );
  }
}
