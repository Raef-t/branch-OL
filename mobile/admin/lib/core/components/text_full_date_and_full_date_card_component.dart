import 'package:flutter/material.dart';
import '/core/components/generate_full_date_cards_component.dart';
import '/core/components/text_arabic_full_date_with_chevron_icons_component.dart';
import '/core/sized_boxs/heights.dart';

class TextFullDateAndFullDateCardComponent extends StatelessWidget {
  const TextFullDateAndFullDateCardComponent({
    super.key,
    this.leftOnPressed,
    this.rightOnPressed,
    this.goToCurrentWeek,
    required this.firstDayInThisWeek,
    required this.onDateSelected,
    required this.circleValues,
    this.selectedDate,
  });
  final void Function()? leftOnPressed, rightOnPressed, goToCurrentWeek;
  final DateTime firstDayInThisWeek;
  final void Function(DateTime selectedDate) onDateSelected;
  final int circleValues;
  final DateTime? selectedDate;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Heights.height29(context: context),
        TextArabicFullDateWithChevronIconsComponent(
          leftOnPressed: leftOnPressed,
          rightOnPressed: rightOnPressed,
          goToCurrentWeek: goToCurrentWeek,
          date: selectedDate,
        ),
        Heights.height12(context: context),
        GenerateFullDateCardsComponent(
          firstDayInThisWeek: firstDayInThisWeek,
          onDateSelected: onDateSelected,
          circleValues: circleValues,
        ),
      ],
    );
  }
}
