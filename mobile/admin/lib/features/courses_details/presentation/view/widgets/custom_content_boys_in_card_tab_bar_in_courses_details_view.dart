import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/managers/cubit/academic_branches_courses_details_cubit.dart';
import '/features/courses_details/presentation/managers/cubit/academic_branches_courses_details_state.dart';
import '/features/courses_details/presentation/view/widgets/custom_success_state_boys_in_courses_details_view.dart';

class CustomContentBoysInCardTabBarInCoursesDetailsView extends StatefulWidget {
  const CustomContentBoysInCardTabBarInCoursesDetailsView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  @override
  State<CustomContentBoysInCardTabBarInCoursesDetailsView> createState() =>
      _CustomContentBoysInCardTabBarInCoursesDetailsViewState();
}

class _CustomContentBoysInCardTabBarInCoursesDetailsViewState
    extends State<CustomContentBoysInCardTabBarInCoursesDetailsView> {
  @override
  void initState() {
    context.read<AcademicBranchesCoursesDetailsCubit>().getBranches(
      genderType: 'male',
    );
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<
      AcademicBranchesCoursesDetailsCubit,
      AcademicBranchesCoursesDetailsState
    >(
      builder: (context, state) {
        if (state is AcademicBranchesSuccessCoursesDetailsState) {
          final selectedBranch = state.listOfAcademicBranchesModelInCubit
              .where(
                (branch) =>
                    branch.courseName ==
                    widget.academicBranchesModel.courseName,
              )
              .toList();
          if (selectedBranch.isEmpty) {
            return const TextSuccessStateButTheDataIsEmptyComponent(
              text: 'لا يوجد شعب لهذا الفرع',
            );
            //firstWhere: throw exception if the branch(element) not found, but where: don't throw exception
          }
          final listOfBatchesModel = selectedBranch.first.listOfBtachesModel;
          final lengthToListOfBatchesModel = listOfBatchesModel?.length ?? 0;
          return CustomSuccessStateBoysInCoursesDetailsView(
            lengthToListOfBatchesModel: lengthToListOfBatchesModel,
            listOfBatchesModel: listOfBatchesModel,
          );
        } else if (state is AcademicBranchesFailureCoursesDetailsState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () => context
                .read<AcademicBranchesCoursesDetailsCubit>()
                .getBranches(genderType: 'male'),
          );
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
