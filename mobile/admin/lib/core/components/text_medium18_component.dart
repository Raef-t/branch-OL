import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextMedium18Component extends StatelessWidget {
  const TextMedium18Component({
    super.key,
    required this.text,
    this.color,
    this.fontFamily,
    this.textDirection,
    this.overflow,
    this.maxLines,
  });
  final String text;
  final Color? color;
  final String? fontFamily;
  final TextDirection? textDirection;
  final TextOverflow? overflow;
  final int? maxLines;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      textDirection: textDirection,
      overflow: overflow,
      maxLines: maxLines,
      style: TextsStyle.medium18(
        context: context,
      ).copyWith(fontFamily: fontFamily, color: color),
    );
  }
}
