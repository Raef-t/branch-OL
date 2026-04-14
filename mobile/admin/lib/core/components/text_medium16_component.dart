import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextMedium16Component extends StatelessWidget {
  const TextMedium16Component({
    super.key,
    required this.text,
    this.color,
    this.fontFamily,
    this.textAlign,
  });
  final String text;
  final Color? color;
  final String? fontFamily;
  final TextAlign? textAlign;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      textAlign: textAlign,
      style: TextsStyle.medium16(
        context: context,
      ).copyWith(fontFamily: fontFamily, color: color),
    );
  }
}
