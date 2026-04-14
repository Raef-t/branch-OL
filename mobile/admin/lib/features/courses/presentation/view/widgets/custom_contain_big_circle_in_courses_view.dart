import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/features/courses/presentation/managers/cubit/total_students/total_students_cubit.dart';
import '/features/courses/presentation/managers/cubit/total_students/total_students_state.dart';
import '/features/courses/presentation/view/widgets/custom_success_state_total_numbers_in_courses_view.dart';

class CustomContainBigCircleInCoursesView extends StatelessWidget {
  const CustomContainBigCircleInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<TotalStudentsCubit, TotalStudentsState>(
      builder: (context, state) {
        if (state is TotalStudentsSuccessState) {
          final totalStudents =
              state.totalStudentsModelInCubit.totalStudents ?? 0;
          return CustomSuccessStateTotalNumbersInCoursesView(
            totalStudents: totalStudents,
          );
        } else if (state is TotalStudentsFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<TotalStudentsCubit>().getTotalStudents(),
          );
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
