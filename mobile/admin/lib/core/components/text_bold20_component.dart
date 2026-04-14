import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextBold20Component extends StatelessWidget {
  const TextBold20Component({
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
      style: TextsStyle.bold20(
        context: context,
      ).copyWith(color: color, fontFamily: fontFamily),
    );
  }
}
