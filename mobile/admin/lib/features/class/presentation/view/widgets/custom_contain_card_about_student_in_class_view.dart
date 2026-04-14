import 'package:flutter/material.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';
import '/features/class/presentation/view/widgets/custom_list_tile_in_class_view.dart';

class CustomContainCardAboutStudentInClassView extends StatelessWidget {
  const CustomContainCardAboutStudentInClassView({
    super.key,
    required this.batchStudentsModel,
    required this.index,
    required this.selectedIndex,
    required this.isVisible,
  });
  final BatchStudentsModel batchStudentsModel;
  final int index, selectedIndex;
  final bool isVisible;
  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: CustomListTileInClassView(
        batchStudentsModel: batchStudentsModel,
        index: index,
        selectedIndex: selectedIndex,
        isVisible: isVisible,
      ),
    );
  }
}
