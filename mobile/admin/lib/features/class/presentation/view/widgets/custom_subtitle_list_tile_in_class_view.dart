import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/styles/colors_style.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';
import '/gen/fonts.gen.dart';

class CustomSubtitleListTileInClassView extends StatelessWidget {
  const CustomSubtitleListTileInClassView({
    super.key,
    required this.batchStudentsModel,
  });
  final BatchStudentsModel batchStudentsModel;
  @override
  Widget build(BuildContext context) {
    return TextMedium12Component(
      text: batchStudentsModel.date ?? 'لا يوجد تاريخ',
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.greyColor,
    );
  }
}
