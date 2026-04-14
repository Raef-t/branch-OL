import 'package:flutter/material.dart';
import '/core/components/checkbox_component.dart';
import '/core/sized_boxs/widths.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';
import '/features/class/presentation/view/widgets/custom_student_image_in_class_view.dart';

class CustomLeadingListTileInClassView extends StatelessWidget {
  const CustomLeadingListTileInClassView({
    super.key,
    required this.index,
    required this.batchStudentsModel,
  });
  final int index;
  final BatchStudentsModel batchStudentsModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        CheckboxComponent(index: index),
        Widths.width10(context: context),
        CustomStudentImageInClassView(batchStudentsModel: batchStudentsModel),
      ],
    );
  }
}
