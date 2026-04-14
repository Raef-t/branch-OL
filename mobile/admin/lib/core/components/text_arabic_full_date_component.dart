import 'package:flutter/material.dart';
import '/core/components/text_medium18_component.dart';
import '/core/helpers/daily_date_in_arabic_helper.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class TextArabicFullDateComponent extends StatelessWidget {
  const TextArabicFullDateComponent({super.key, this.onTap, this.date});
  final VoidCallback? onTap;
  final DateTime? date;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: TextMedium18Component(
        text: dailyDateInArabicHelper(date: date),
        color: ColorsStyle.littleBlackColor,
        fontFamily: FontFamily.poppins,
      ),
    );
  }
}
