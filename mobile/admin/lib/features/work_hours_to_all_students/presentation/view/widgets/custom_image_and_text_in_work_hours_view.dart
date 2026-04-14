import 'package:flutter/material.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/texts_style.dart';
import '/gen/fonts.gen.dart';

class CustomImageAndTextInWorkHoursView extends StatelessWidget {
  const CustomImageAndTextInWorkHoursView({
    super.key,
    required this.image,
    required this.text,
  });
  final Image image;
  final String text;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        image,
        Widths.width10(context: context),
        Text(
          text,
          style: TextsStyle.medium16(
            context: context,
          ).copyWith(fontFamily: FontFamily.tajawal),
        ),
      ],
    );
  }
}
