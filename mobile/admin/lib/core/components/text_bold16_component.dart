import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextBold16Component extends StatelessWidget {
  const TextBold16Component({
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
      style: TextsStyle.bold16(
        context: context,
      ).copyWith(color: color, fontFamily: fontFamily),
    );
  }
}
