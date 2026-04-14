import 'package:flutter/material.dart';
import '/core/components/text_arabic_full_date_component.dart';

class TextArabicFullDateWithChevronIconsComponent extends StatelessWidget {
  const TextArabicFullDateWithChevronIconsComponent({
    super.key,
    required this.leftOnPressed,
    required this.rightOnPressed,
    this.goToCurrentWeek,
    this.date,
  });
  final void Function()? leftOnPressed, rightOnPressed, goToCurrentWeek;
  final DateTime? date;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        IconButton(
          onPressed: leftOnPressed,
          icon: const Icon(Icons.chevron_left),
        ),
        TextArabicFullDateComponent(onTap: goToCurrentWeek, date: date),
        IconButton(
          onPressed: rightOnPressed,
          icon: const Icon(Icons.chevron_right),
        ),
      ],
    );
  }
}
