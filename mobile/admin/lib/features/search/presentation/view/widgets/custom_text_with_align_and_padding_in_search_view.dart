import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';

class CustomTextWithAlignAndPaddingInSearchView extends StatelessWidget {
  const CustomTextWithAlignAndPaddingInSearchView({
    super.key,
    required this.text,
  });
  final String text;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right25(
      context: context,
      child: Align(
        alignment: Alignment.centerRight,
        child: TextMedium14Component(text: text),
      ),
    );
  }
}
