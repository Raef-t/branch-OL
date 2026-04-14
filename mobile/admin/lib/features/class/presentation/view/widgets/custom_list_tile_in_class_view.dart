import 'package:flutter/material.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';
import '/features/class/presentation/view/widgets/custom_leading_list_tile_in_class_view.dart';
import '/features/class/presentation/view/widgets/custom_subtitle_list_tile_in_class_view.dart';
import '/features/class/presentation/view/widgets/custom_title_list_tile_in_class_view.dart';
import '/features/class/presentation/view/widgets/custom_trailing_list_tile_in_class_view.dart';

class CustomListTileInClassView extends StatelessWidget {
  const CustomListTileInClassView({
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
    return ListTile(
      leading: CustomLeadingListTileInClassView(
        index: index,
        batchStudentsModel: batchStudentsModel,
      ),
      title: CustomTitleListTileInClassView(
        batchStudentModel: batchStudentsModel,
      ),
      subtitle: CustomSubtitleListTileInClassView(
        batchStudentsModel: batchStudentsModel,
      ),
      trailing: Visibility(
        visible: isVisible,
        child: CustomTrailingListTileInClassView(
          batchStudentsModel: batchStudentsModel,
          selectedIndex: selectedIndex,
        ),
      ),
    );
  }
}
