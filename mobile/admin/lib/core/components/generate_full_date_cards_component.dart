import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '/core/components/full_date_card_selected_component.dart';
import '/core/lists/date_and_days_in_exam_view_list.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';

class GenerateFullDateCardsComponent extends StatefulWidget {
  const GenerateFullDateCardsComponent({
    super.key,
    required this.firstDayInThisWeek,
    required this.onDateSelected,
    required this.circleValues,
  });
  final DateTime firstDayInThisWeek;
  final void Function(DateTime selectedDate) onDateSelected;
  final int circleValues;
  @override
  State<GenerateFullDateCardsComponent> createState() =>
      _GenerateFullDateCardsComponentState();
}

class _GenerateFullDateCardsComponentState
    extends State<GenerateFullDateCardsComponent> {
  int selectedCard = -1;
  @override
  void initState() {
    super.initState();
    setDefaultSelectedCard();
  } //the default selected card will appear after first rebuild for first time

  bool get isCurrentWeek {
    final now = DateTime.now();
    final firstDayOfCurrentWeek = now.subtract(Duration(days: now.weekday - 1));
    return widget.firstDayInThisWeek.year == firstDayOfCurrentWeek.year &&
        widget.firstDayInThisWeek.month == firstDayOfCurrentWeek.month &&
        widget.firstDayInThisWeek.day == firstDayOfCurrentWeek.day;
  } //this method to tell me if i in current week and current day

  int get getOnIndexToday {
    final today = DateTime.now();
    return today.weekday - 1;
  } //i take index the current day in this current week

  void setDefaultSelectedCard() {
    if (isCurrentWeek) {
      selectedCard = getOnIndexToday;
    } else {
      selectedCard = -1;
    }
  } //know i will make default selected card for current day in this current week

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left22AndRight21(
      context: context,
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: List.generate(dateAndDaysInExamViewList.length, (index) {
            final day = widget.firstDayInThisWeek.add(Duration(days: index));
            return FullDateCardSelectedComponent(
              date: DateFormat('d', 'ar').format(day), //18
              day: DateFormat('EEE', 'ar').format(day), // Mon
              isSelectedCard: selectedCard == index,
              circleValues: widget.circleValues,
              onTap: () {
                setState(() => selectedCard = index);
                widget.onDateSelected(day);
              },
            );
          }),
        ),
      ),
    );
  }
}
