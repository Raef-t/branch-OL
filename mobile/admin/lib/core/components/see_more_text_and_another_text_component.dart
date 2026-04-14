import 'package:flutter/material.dart';

class SeeMoreTextAndAnotherTextComponent extends StatelessWidget {
  const SeeMoreTextAndAnotherTextComponent({
    super.key,
    required this.text,
    required this.textStyleToAnotherText,
    required this.textStyleToSeeMoreText,
    required this.onTap,
  });
  final String text;
  final TextStyle textStyleToAnotherText, textStyleToSeeMoreText;
  final void Function() onTap;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        GestureDetector(
          onTap: onTap,
          child: Text('رؤية المزيد', style: textStyleToSeeMoreText),
        ),
        const Spacer(),
        Text(text, style: textStyleToAnotherText),
      ],
    );
  }
}
