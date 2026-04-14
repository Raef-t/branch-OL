import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextMedium12Component extends StatelessWidget {
  const TextMedium12Component({
    super.key,
    required this.text,
    this.color,
    this.fontFamily,
    this.textDirection,
  });
  final String text;
  final Color? color;
  final String? fontFamily;
  final TextDirection? textDirection;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      textDirection: textDirection,
      style: TextsStyle.medium12(
        context: context,
      ).copyWith(color: color, fontFamily: fontFamily),
    );
  }
}
