import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/exams_to_student/presentation/managers/cubits/student_exams_cubit.dart';
import '/features/exams_to_student/presentation/managers/cubits/student_exams_state.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_card_in_exams_to_student_view.dart';

class CustomGenerateCurrentWeekCardsInExamsToStudentView
    extends StatelessWidget {
  const CustomGenerateCurrentWeekCardsInExamsToStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<StudentExamsCubit, StudentExamsState>(
      builder: (context, state) {
        if (state is StudentExamsSuccessState) {
          final length = state.data.lastList.length;
          if (state.data.lastList.isEmpty) {
            return const TextSuccessStateButTheDataIsEmptyComponent(
              text: 'لا يوجد',
            );
          }
          return Column(
            children: List.generate(length, (index) {
              final currentModel = state.data.lastList[index];
              return CustomCardInExamsToStudentView(
                subjectName: currentModel.subjectName ?? '',
                date: currentModel.date ?? '',
                course: currentModel.course ?? '',
                classRoom: currentModel.classRoom ?? '',
              );
            }),
          );
        } else if (state is StudentExamsFailureState) {
          return FailureStateComponent(errorText: state.errorMessage);
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
