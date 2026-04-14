import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/text_full_date_and_full_date_card_component.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/sized_boxs/heights.dart';
import '/features/work_hours_to_all_students/presentation/managers/cubits/schedule_to_all_student_cubit.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_generate_menu_cards_in_work_hours_view.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_text_with_padding_in_work_hours_view.dart';

class CustomSliverFillRemainingInWorkHoursView extends StatefulWidget {
  const CustomSliverFillRemainingInWorkHoursView({super.key});

  @override
  State<CustomSliverFillRemainingInWorkHoursView> createState() =>
      _CustomSliverFillRemainingInWorkHoursViewState();
}

class _CustomSliverFillRemainingInWorkHoursViewState
    extends State<CustomSliverFillRemainingInWorkHoursView> {
  int lengthWorkHours = 0;
  DateTime selectedDate = DateTime.now();
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
    if (next.isAfter(currentWeek)) return;
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
              leftOnPressed: previousWeek,
              rightOnPressed: firstDayInThisWeek.isAtSameMomentAs(currentWeek)
                  ? null
                  : nextWeek,
              goToCurrentWeek: goToCurrentWeek,
              circleValues: lengthWorkHours,
              selectedDate: selectedDate,
              onDateSelected: (selectedDateInFunction) {
                setState(() {
                  selectedDate = selectedDateInFunction;
                });
                String formattedDate = DateFormat(
                  'EEEE',
                  'en',
                ).format(selectedDateInFunction);
                context.read<ScheduleToAllStudentCubit>().getSchedule(
                  day: formattedDate,
                );
              },
            ),

            Heights.height20(context: context),
            const CustomTextWithPaddingInWorkHoursView(),
            Heights.height25(context: context),
            CustomGenerateMenuCardsInWorkHoursView(
              onLengthWorkHours: (value) {
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  setState(() {
                    lengthWorkHours = value;
                  });
                });
              },
            ),
          ],
        ),
      ),
    );
  }
}
