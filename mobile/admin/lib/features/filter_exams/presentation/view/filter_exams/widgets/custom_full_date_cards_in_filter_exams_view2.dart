import 'package:flutter/material.dart';
import '/core/components/generate_full_date_cards_component.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';

class CustomFullDateCardsInFilterExamsView2 extends StatelessWidget {
  const CustomFullDateCardsInFilterExamsView2({
    super.key,
    required this.firstDayInThisWeek,
  });
  final DateTime firstDayInThisWeek;
  @override
  Widget build(BuildContext context) {
    return SymmetricPaddingWithChild.horizontal1(
      context: context,
      child: GenerateFullDateCardsComponent(
        firstDayInThisWeek: firstDayInThisWeek,
        onDateSelected: (selectedDate) {},
        circleValues: 0,
      ),
    );
  }
}
