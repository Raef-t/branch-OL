import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';

class TextSemiBold10Component extends StatelessWidget {
  const TextSemiBold10Component({
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
      style: TextsStyle.semiBold10(
        context: context,
      ).copyWith(fontFamily: fontFamily, color: ColorsStyle.blackColor),
    );
  }
}
