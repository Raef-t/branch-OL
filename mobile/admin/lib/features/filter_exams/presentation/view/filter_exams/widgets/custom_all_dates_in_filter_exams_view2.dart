import 'package:flutter/material.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/sized_boxs/heights.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_full_date_cards_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_full_date_text_with_date_image_in_filter_exams_view2.dart';

class CustomAllDatesInFilterExamsView2 extends StatefulWidget {
  const CustomAllDatesInFilterExamsView2({super.key});

  @override
  State<CustomAllDatesInFilterExamsView2> createState() =>
      _CustomAllDatesInFilterExamsView2State();
}

class _CustomAllDatesInFilterExamsView2State
    extends State<CustomAllDatesInFilterExamsView2> {
  late final DateTime currentWeek;
  //this parameter to know if i in current week to make rightOnPressed method not work
  DateTime firstDayInThisWeek = giveFirstDayInThisWeekFunction(
    date: DateTime.now(),
  );
  //i choose first day(i mean day i make this operation logic work) in this week and always make this day appear in the first
  @override
  void initState() {
    super.initState();
    currentWeek = giveFirstDayInThisWeekFunction(date: DateTime.now());
  }

  static DateTime giveFirstDayInThisWeekFunction({required DateTime date}) {
    return date.subtract(Duration(days: date.weekday - 1));
    //i give first day in this week but subtract deal with array so the days it's(1,2,..7) but the subtract method it's cut from index 0 i mean it's deal with indexes
  } //i put static because DateTime is Object and to enable take value from Function type it DateTime you should put static

  void nextWeek() {
    final next = firstDayInThisWeek.add(k7Days);
    //the next it's same values firstDayInThisWeek(7 days), but days in the next week(add)
    final now = DateTime.now();
    final isCurrentMonth = next.year == now.year && next.month == now.month;
    if (!isCurrentMonth) return;
    //i check if the value next parameter it's after value currentWeek parameter, so if true so make click on this method not return anything(not work)
    setState(() => firstDayInThisWeek = next);
    //if false so add this 7 days(next week)
  } //when i use this method you should add 7 days(so will appear next week)

  void previousWeek() {
    setState(() => firstDayInThisWeek = firstDayInThisWeek.subtract(k7Days));
  } //when i use this method you should back 7 days(so will appear previous week)

  void goToCurrentWeek() {
    setState(() => firstDayInThisWeek = currentWeek);
  } //when i click on TextArabicText i will return to current week

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        CustomFullDateTextWithDateImageInFilterExamsView2(
          previousWeek: previousWeek,
          nextWeek: nextWeek,
          goToCurrentWeek: goToCurrentWeek,
        ),
        Heights.height22AndHalf(context: context),
        CustomFullDateCardsInFilterExamsView2(
          firstDayInThisWeek: firstDayInThisWeek,
        ),
      ],
    );
  }
}
