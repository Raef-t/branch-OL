import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/filter_exams/presentation/managers/cubits/subjects/subjects_cubit.dart';
import '/features/filter_exams/presentation/managers/cubits/subjects/subjects_state.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_success_state_for_subjects_in_filter_exams_view2.dart';

class CustomGenerateSubjectCardsInFilterExamsView2 extends StatelessWidget {
  const CustomGenerateSubjectCardsInFilterExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<SubjectsCubit, SubjectsState>(
      builder: (context, state) {
        if (state is SubjectsSuccessState) {
          final listOfSubjectsModel = state.listOfSubjectsModelInCubit;
          final length = listOfSubjectsModel.length;
          if (listOfSubjectsModel.isEmpty) {
            return const TextSuccessStateButTheDataIsEmptyComponent(
              text: 'لا يوجد مواد',
            );
          }
          return CustomSuccessStateForSubjectsInFilterExamsView2(
            length: length,
            listOfSubjectsModel: listOfSubjectsModel,
          );
        } else if (state is SubjectsFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<SubjectsCubit>().getSubjectsByAcademicBranch(),
          );
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
