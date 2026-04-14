import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextMedium13Component extends StatelessWidget {
  const TextMedium13Component({
    super.key,
    required this.text,
    this.color,
    this.fontFamily,
  });
  final String text;
  final Color? color;
  final String? fontFamily;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      textAlign: TextAlign.end,
      style: TextsStyle.medium13(
        context: context,
      ).copyWith(color: color, fontFamily: fontFamily),
    );
  }
}
