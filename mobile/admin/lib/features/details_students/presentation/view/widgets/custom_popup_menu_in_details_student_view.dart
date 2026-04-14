import 'package:flutter/material.dart';
import '/core/helpers/build_item_builder_in_popup_menu_in_details_student_helper.dart';
import '/features/details_students/presentation/view/widgets/custom_text_with_drop_icon_in_details_student_view.dart';

class CustomPopupMenuInDetailsStudentView extends StatelessWidget {
  const CustomPopupMenuInDetailsStudentView({
    super.key,
    required this.selectedValue,
    required this.onSelected,
  });
  final String selectedValue;
  final void Function(String) onSelected;
  @override
  Widget build(BuildContext context) {
    return PopupMenuButton<String>(
      onSelected: onSelected,
      itemBuilder: buildItemBuilderInPopupMenuInDetailsStudentHelper(),
      child: CustomTextWithDropIconInDetailsStudentView(text: selectedValue),
    );
  }
}
