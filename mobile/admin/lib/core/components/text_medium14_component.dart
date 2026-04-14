import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextMedium14Component extends StatelessWidget {
  const TextMedium14Component({
    super.key,
    required this.text,
    this.color,
    this.textAlign,
  });
  final String text;
  final Color? color;
  final TextAlign? textAlign;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      textAlign: textAlign,
      style: TextsStyle.medium14(context: context).copyWith(color: color),
    );
  }
}
