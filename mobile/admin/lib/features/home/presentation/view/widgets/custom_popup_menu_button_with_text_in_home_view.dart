import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/styles/texts_style.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';
import '/features/home/presentation/view/widgets/custom_popup_menu_button_in_home_view.dart';

class CustomPopupMenuButtonWithTextInHomeView extends StatelessWidget {
  const CustomPopupMenuButtonWithTextInHomeView({
    super.key,
    required this.selectedValue,
    required this.onSelected,
  });
  final AcademicBranchesToCoursesModel? selectedValue;
  final void Function(AcademicBranchesCoursesDetailsModel) onSelected;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left20AndRight22AndBottom42(
      context: context,
      child: Row(
        children: [
          CustomPopupMenuButtonInHomeView(
            onSelected: onSelected,
            selectedValue: selectedValue,
          ),
          const Spacer(),
          Text('الدورة المتفوقة', style: TextsStyle.medium16(context: context)),
        ],
      ),
    );
  }
}
