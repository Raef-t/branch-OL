import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/text_full_date_and_full_date_card_component.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/sized_boxs/heights.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_cubit.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_exam_and_date_two_texts_in_exam_view.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_generate_exam_and_time_cards_in_exam_view.dart';

class CustomSliverFillRemainingToExamView extends StatefulWidget {
  const CustomSliverFillRemainingToExamView({super.key});

  @override
  State<CustomSliverFillRemainingToExamView> createState() =>
      _CustomSliverFillRemainingToExamViewState();
}

class _CustomSliverFillRemainingToExamViewState
    extends State<CustomSliverFillRemainingToExamView> {
  int lengthExams = 0;
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
    String formattedDate = DateFormat('yyyy-MM-dd').format(DateTime.now());
    context.read<ExamsCubit>().getExamsByDate(date: formattedDate);
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
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            TextFullDateAndFullDateCardComponent(
              firstDayInThisWeek: firstDayInThisWeek,
              rightOnPressed: nextWeek,
              leftOnPressed: previousWeek,
              goToCurrentWeek: goToCurrentWeek,
              circleValues: lengthExams,
              onDateSelected: (selectedDate) {
                String formattedDate = DateFormat(
                  'yyyy-MM-dd',
                ).format(selectedDate);
                context.read<ExamsCubit>().getExamsByDate(date: formattedDate);
              },
            ),
            Heights.height34(context: context),
            const CustomExamAndDateTwoTextsInExamView(),
            Heights.height20(context: context),
            CustomGenerateExamAndTimeCardsInExamView(
              onLengthExams: (value) {
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  //this WidgetsBinding.. it's mean i want to do rebuild after finish current rebuild(i mean when the cubit finish from rebuild so do rebuild again here), this thing is important because flutter reject do rebuild again directly after last rebuild
                  if (!mounted) return;
                  //if user don't getout from this view so the widget is still founded(mounted = true) so don't getout from this method because you can do rebuild
                  setState(() => lengthExams = value);
                });
              },
            ),
            Heights.height20(context: context),
          ],
        ),
      ),
    );
  }
}
