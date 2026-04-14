import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:intl/intl.dart';
import '/core/components/failure_state_component.dart';
import '/features/home/presentation/view/widgets/shimmer_exams_card_home_view.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_cubit.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_state.dart';
import '/features/home/presentation/view/widgets/custom_success_state_for_exam_numbers_today_card_in_home_view.dart';

class CustomContainExamNumbersTodayCardHomeView extends StatelessWidget {
  const CustomContainExamNumbersTodayCardHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<ExamsCubit, ExamsState>(
      builder: (context, state) {
        if (state is ExamsSuccessState) {
          final length = state.examsListInCubit.length;
          final examsModelList = state.examsListInCubit;

          return CustomSuccessStateForExamNumbersTodayCardInHomeView(
            length: length,
            examsModelList: examsModelList,
          );
        } else if (state is ExamsFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () {
              final date = DateFormat('yyyy-MM-dd').format(DateTime.now());
              context.read<ExamsCubit>().getExamsByDate(date: date);
            },
          );
        } else {
          return const ShimmerExamsCardHomeView();
        }
      },
    );
  }
}
