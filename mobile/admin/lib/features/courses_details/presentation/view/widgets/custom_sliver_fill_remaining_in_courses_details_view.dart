import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_card_tab_bar_in_courses_details_view.dart';
import '/features/courses_details/presentation/view/widgets/custom_content_all_in_card_tab_bar_in_courses_details_view.dart';
import '/features/courses_details/presentation/view/widgets/custom_content_boys_in_card_tab_bar_in_courses_details_view.dart';
import '/features/courses_details/presentation/view/widgets/custom_content_girls_in_card_tab_bar_in_courses_details_view.dart';

class CustomSliverFillRemainingInCoursesDetailsView extends StatelessWidget {
  const CustomSliverFillRemainingInCoursesDetailsView({
    super.key,
    required this.selectedIndex,
    required this.onTapSelected,
    required this.academicBranchesModel,
  });
  final int selectedIndex;
  final ValueChanged<int> onTapSelected;
  final AcademicBranchesToCoursesModel academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height22(context: context),
            CustomCardTabBarInCoursesDetailsView(
              selectedIndex: selectedIndex,
              onTapSelected: onTapSelected,
            ),
            Heights.height24(context: context),
            if (selectedIndex == 0)
              CustomContentGirlsInCardTabBarInCoursesDetailsView(
                academicBranchesModel: academicBranchesModel,
              ),
            if (selectedIndex == 1)
              CustomContentBoysInCardTabBarInCoursesDetailsView(
                academicBranchesModel: academicBranchesModel,
              ),
            if (selectedIndex == 2)
              CustomContentAllInCardTabBarInCoursesDetailsView(
                academicBranchesModel: academicBranchesModel,
              ),
          ],
        ),
      ),
    );
  }
}
