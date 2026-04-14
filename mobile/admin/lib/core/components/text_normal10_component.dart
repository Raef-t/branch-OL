import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class TextNormal10Component extends StatelessWidget {
  const TextNormal10Component({
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
      style: TextsStyle.normal10(
        context: context,
      ).copyWith(fontFamily: fontFamily, color: color),
    );
  }
}
