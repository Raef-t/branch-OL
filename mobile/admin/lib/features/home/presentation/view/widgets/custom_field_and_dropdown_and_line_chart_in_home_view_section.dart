import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';
import '/features/home/presentation/view/widgets/custom_header_item_section_in_home_view.dart';
import '/features/home/presentation/view/widgets/custom_line_chart_in_home_view.dart';
import '/features/home/presentation/view/widgets/custom_popup_menu_button_with_text_in_home_view.dart';

class CustomFieldAndDropdownAndLineChartInHomeViewSection
    extends StatelessWidget {
  const CustomFieldAndDropdownAndLineChartInHomeViewSection({
    super.key,
    required this.selectedValue,
    required this.onSelected,
  });
  final AcademicBranchesToCoursesModel? selectedValue;
  final void Function(AcademicBranchesCoursesDetailsModel) onSelected;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        const CustomHeaderItemSectionInHomeView(),
        Heights.height34(context: context),
        CustomPopupMenuButtonWithTextInHomeView(
          selectedValue: selectedValue,
          onSelected: onSelected,
        ),
        const CustomLineChartInHomeView(),
      ],
    );
  }
}
