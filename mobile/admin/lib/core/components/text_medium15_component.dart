import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextMedium15Component extends StatelessWidget {
  const TextMedium15Component({super.key, required this.text, this.color});
  final String text;
  final Color? color;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: TextsStyle.medium15(context: context).copyWith(color: color),
    );
  }
}
