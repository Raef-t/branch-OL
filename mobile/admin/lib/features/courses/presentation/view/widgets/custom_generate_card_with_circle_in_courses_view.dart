import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:second_page_app/features/exams_to_all_students/presentation/view/widgets/shimmer_exams_view_cards.dart';
import '/core/components/failure_state_component.dart';
import '/features/courses/presentation/managers/cubit/academic_branches/academic_branches_cubit.dart';
import '/features/courses/presentation/managers/cubit/academic_branches/academic_branches_state.dart';
import '/features/courses/presentation/view/widgets/custom_success_state_branches_in_courses_view.dart';

class CustomGenerateCardWithCircleInCoursesView extends StatelessWidget {
  const CustomGenerateCardWithCircleInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AcademicBranchesCubit, AcademicBranchesState>(
      builder: (context, state) {
        if (state is AcademicBranchesSuccessState) {
          final listOfAcademicBranchesModel =
              state.listOfAcademicBranchesModelInCubit;
          final length = listOfAcademicBranchesModel.length;
          return CustomSuccessStateBranchesInCoursesView(
            length: length,
            listOfAcademicBranchesModel: listOfAcademicBranchesModel,
          );
        } else if (state is AcademicBranchesFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<AcademicBranchesCubit>().getBranches(),
          );
        } else {
          return const ShimmerExamsViewCards();
        }
      },
    );
  }
}
