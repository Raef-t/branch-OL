import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextMedium10Component extends StatelessWidget {
  const TextMedium10Component({
    super.key,
    required this.text,
    this.color,
    this.fontFamily,
    this.overflow,
    this.maxLines,
    this.textAlign,
  });
  final String text;
  final Color? color;
  final String? fontFamily;
  final TextOverflow? overflow;
  final int? maxLines;
  final TextAlign? textAlign;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      overflow: overflow,
      maxLines: maxLines,
      textAlign: textAlign,
      style: TextsStyle.medium10(
        context: context,
      ).copyWith(color: color, fontFamily: fontFamily),
    );
  }
}
