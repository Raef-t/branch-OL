import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextBold13Component extends StatelessWidget {
  const TextBold13Component({super.key, required this.text, this.color});
  final String text;
  final Color? color;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: TextsStyle.bold13(context: context).copyWith(color: color),
    );
  }
}
