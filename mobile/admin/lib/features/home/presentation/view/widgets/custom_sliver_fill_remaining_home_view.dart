import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';
import '/features/home/presentation/view/widgets/custom_exams_and_number_exams_today_home_view_section.dart';
import '/features/home/presentation/view/widgets/custom_field_and_dropdown_and_line_chart_in_home_view_section.dart';
import '/features/home/presentation/view/widgets/custom_work_hours_home_view_section.dart';

class CustomSliverFillRemainingHomeView extends StatelessWidget {
  const CustomSliverFillRemainingHomeView({
    super.key,
    required this.selectedValue,
    required this.onSelected,
  });
  final AcademicBranchesToCoursesModel? selectedValue;
  final void Function(AcademicBranchesCoursesDetailsModel) onSelected;
  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height30(context: context),
            CustomFieldAndDropdownAndLineChartInHomeViewSection(
              selectedValue: selectedValue,
              onSelected: onSelected,
            ),
            Heights.height25(context: context),
            const CustomExamsAndNumberExamsTodayHomeViewSection(),
            Heights.height10(context: context),
            const CustomWorkHoursHomeViewSection(),
          ],
        ),
      ),
    );
  }
}
