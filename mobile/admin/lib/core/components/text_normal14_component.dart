import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextNormal14Component extends StatelessWidget {
  const TextNormal14Component({super.key, required this.text, this.color});
  final String text;
  final Color? color;
  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: TextsStyle.normal14(context: context).copyWith(color: color),
    );
  }
}
