import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/features/class/presentation/managers/cubits/batch_students/batch_students_cubit.dart';
import '/features/class/presentation/managers/cubits/batch_students/batch_students_state.dart';
import '/features/class/presentation/view/widgets/custom_success_state_for_cards_in_class_view.dart';

class CustomGenerateCardsAboutStudentInClassView extends StatelessWidget {
  const CustomGenerateCardsAboutStudentInClassView({
    super.key,
    required this.isVisible,
    required this.selectedIndex,
  });
  final bool isVisible;
  final int selectedIndex;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<BatchStudentsCubit, BatchStudentsState>(
      builder: (context, state) {
        if (state is BatchStudentsSuccessState) {
          final listOfBatchStudentsModel =
              state.listOfBatchStudentsModelInCubit;
          final length = listOfBatchStudentsModel.length;
          return CustomSuccessStateForCardsInClassView(
            length: length,
            listOfBatchStudentsModel: listOfBatchStudentsModel,
            selectedIndex: selectedIndex,
            isVisible: isVisible,
          );
        } else if (state is BatchStudentsFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () => context.read<BatchStudentsCubit>().getStudents(),
          );
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
