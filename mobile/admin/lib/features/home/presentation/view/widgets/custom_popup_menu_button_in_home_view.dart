import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/failure_state_component.dart';
import '/features/home/presentation/view/widgets/shimmer_popup_menu_home_view.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/managers/cubit/academic_branches_courses_details_cubit.dart';
import '/features/courses_details/presentation/managers/cubit/academic_branches_courses_details_state.dart';
import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';
import '/features/home/presentation/view/widgets/custom_icon_and_text_in_popup_menu_item_home_view.dart';

class CustomPopupMenuButtonInHomeView extends StatelessWidget {
  const CustomPopupMenuButtonInHomeView({
    super.key,
    required this.onSelected,
    required this.selectedValue,
  });
  final void Function(AcademicBranchesCoursesDetailsModel) onSelected;
  final AcademicBranchesToCoursesModel? selectedValue;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<
      AcademicBranchesCoursesDetailsCubit,
      AcademicBranchesCoursesDetailsState
    >(
      builder: (context, state) {
        if (state is AcademicBranchesSuccessCoursesDetailsState) {
          final branches = state.listOfAcademicBranchesModelInCubit;
          return PopupMenuButton<AcademicBranchesCoursesDetailsModel>(
            borderRadius: Circulars.circular5(context: context),
            onSelected: onSelected,
            itemBuilder: (context) => branches.map((branch) {
              final isSelected = selectedValue?.id == branch.id;
              return PopupMenuItem<AcademicBranchesCoursesDetailsModel>(
                value: branch,
                child: Text(
                  branch.courseName ?? '',
                  style: TextsStyle.normal10(context: context).copyWith(
                    color: isSelected
                        ? ColorsStyle.mediumRussetColor2
                        : ColorsStyle.mediumBrownColor,
                  ),
                ),
              );
            }).toList(),
            child: CustomIconAndTextInPopupMenuItemHomeView(
              selectedValue: selectedValue?.courseName ?? 'الفرع',
            ),
          );
        } else if (state is AcademicBranchesFailureCoursesDetailsState) {
          return FailureStateComponent(errorText: state.errorMessageInCubit);
        } else {
          return const ShimmerPopupMenuHomeView();
        }
      },
    );
  }
}
