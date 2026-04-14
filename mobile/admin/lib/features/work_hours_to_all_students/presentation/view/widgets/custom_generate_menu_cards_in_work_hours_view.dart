import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/menu_card_component.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/work_hours_to_all_students/presentation/managers/cubits/schedule_to_all_student_cubit.dart';
import '/features/work_hours_to_all_students/presentation/managers/cubits/schedule_to_all_student_state.dart';

class CustomGenerateMenuCardsInWorkHoursView extends StatelessWidget {
  const CustomGenerateMenuCardsInWorkHoursView({
    super.key,
    required this.onLengthWorkHours,
  });
  final ValueChanged<int> onLengthWorkHours;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<ScheduleToAllStudentCubit, ScheduleToAllStudentState>(
      builder: (context, state) {
        if (state is ScheduleToAllStudentSuccessState) {
          final scheduleToBatchModel = state.scheduleToBatchModelInCubit;
          final lengthAllCourses = scheduleToBatchModel.periodsCount;
          final listOfPeriodsModel = scheduleToBatchModel.listOfPeriodsModel;
          onLengthWorkHours(lengthAllCourses ?? 0);
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
        } else if (state is ScheduleToAllStudentFailureState) {
          return FailureStateComponent(errorText: state.errorMessageInCubit);
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
