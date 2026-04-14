import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';

class CustomTitleListTileInClassView extends StatelessWidget {
  const CustomTitleListTileInClassView({
    super.key,
    required this.batchStudentModel,
  });
  final BatchStudentsModel batchStudentModel;
  @override
  Widget build(BuildContext context) {
    return TextMedium14Component(
      text: batchStudentModel.fullName ?? 'لا يوجد اسم',
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
