import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:intl/intl.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_two_texts_component.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/menu_card_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/core/components/text_full_date_and_full_date_card_component.dart';
import '/core/components/text_medium16_component.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/heights.dart';
import '/features/work_hours_to_student/presentation/managers/cubits/schedule_to_student_cubit.dart';
import '/features/work_hours_to_student/presentation/managers/cubits/schedule_to_student_state.dart';
import '/gen/fonts.gen.dart';

class CustomWorkHoursToStudentViewBody extends StatefulWidget {
  const CustomWorkHoursToStudentViewBody({super.key});

  @override
  State<CustomWorkHoursToStudentViewBody> createState() =>
      _CustomWorkHoursToStudentViewBodyState();
}

class _CustomWorkHoursToStudentViewBodyState
    extends State<CustomWorkHoursToStudentViewBody> {
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
    return CustomScrollView(
      slivers: [
        const SliverAppBarToHoleAppComponent(
          appBarWidget: AppBarWidgetWithRightArrowImageAndTwoTextsComponent(
            firstText: 'البرنامج اليومي',
            secondText: 'يمكنك الاطلاع على برنامج دوام الطالب',
          ),
        ),
        SliverFillRemaining(
          hasScrollBody: false,
          child: BackgroundBodyToViewsComponent(
            child: Column(
              children: [
                TextFullDateAndFullDateCardComponent(
                  firstDayInThisWeek: firstDayInThisWeek,
                  leftOnPressed: previousWeek,
                  rightOnPressed:
                      firstDayInThisWeek.isAtSameMomentAs(currentWeek)
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
                    context.read<ScheduleToStudentCubit>().getSchedule(
                      day: formattedDate,
                    );
                  },
                ),
                Heights.height20(context: context),
                OnlyPaddingWithChild.right18(
                  context: context,
                  child: const Align(
                    alignment: Alignment.centerRight,
                    child: TextMedium16Component(
                      text: 'البرنامج اليومي',
                      fontFamily: FontFamily.tajawal,
                    ),
                  ),
                ),
                Heights.height25(context: context),
                CustomWorkHoursToStudent(
                  onLengthWorkHourse: (value) {
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
        ),
      ],
    );
  }
}

class CustomWorkHoursToStudent extends StatelessWidget {
  const CustomWorkHoursToStudent({super.key, required this.onLengthWorkHourse});
  final ValueChanged<int> onLengthWorkHourse;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<ScheduleToStudentCubit, ScheduleToStudentState>(
      builder: (context, state) {
        if (state is ScheduleToStudentSuccessState) {
          final scheduleToStudentModel = state.scheduleToStudentModelInCubit;
          final lengthAllCourses = scheduleToStudentModel.periodsCount;
          final listOfPeriodsModel = scheduleToStudentModel.listOfPeriodsModel;
          onLengthWorkHourse(lengthAllCourses ?? 0);
          if (listOfPeriodsModel.isEmpty) {
            return const TextSuccessStateButTheDataIsEmptyComponent(
              text: 'لا يوجد دوام',
            );
          }
          return Column(
            children: List.generate(lengthAllCourses ?? 0, (index) {
              final listOfLessonModel =
                  listOfPeriodsModel[index].listOfLessonModel;
              final lengthListOfLessonModel = listOfLessonModel.length;
              return Column(
                children: List.generate(lengthListOfLessonModel, (index) {
                  final lessonModel = listOfLessonModel[index];
                  return MenuCardComponent(
                    subjectName: lessonModel.subjectName ?? 'لا يوجد',
                    course: lessonModel.course ?? 'دورة',
                    classRoom: lessonModel.classRoom ?? 'قاعه',
                    type: lessonModel.type ?? 'درس',
                    startTime: lessonModel.startTime ?? '09:00 am',
                    endTime: lessonModel.endTime ?? '10:00 am',
                  );
                }),
              );
            }),
          );
        } else if (state is ScheduleToStudentFailureState) {
          return FailureStateComponent(errorText: state.errorMessageInCubit);
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
