import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextMedium32Component extends StatelessWidget {
  const TextMedium32Component({super.key, required this.text, this.color});
  final String text;
  final Color? color;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: TextsStyle.medium32(context: context).copyWith(color: color),
    );
  }
}
