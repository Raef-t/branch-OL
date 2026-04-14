import 'package:flutter/material.dart';
import '/core/classes/selection_controller_class.dart';
import '/core/decorations/box_decorations.dart';
import '/core/lists/student_name_to_title_in_list_tile_in_class_view_list.dart';

class CustomCheckboxSelectAllInClassView extends StatelessWidget {
  const CustomCheckboxSelectAllInClassView({
    super.key,
    required this.controller,
  });
  final SelectionControllerClass controller;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return GestureDetector(
      onTap: () {
        controller.toggleSelectAll(
          totalStudents: studentNameToTitleInListTileInClassViewList.length,
        );
      },
      child: Container(
        height: size.height * (isRotait ? 0.02 : 0.04),
        width: size.width * 0.033,
        decoration: BoxDecorations.boxDecorationToCheckboxSelectAllInClassView(
          isChecked: controller.selectAll,
        ),
      ),
    );
  }
}
