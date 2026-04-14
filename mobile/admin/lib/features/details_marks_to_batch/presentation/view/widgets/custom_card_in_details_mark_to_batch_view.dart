import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/styles/colors_style.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_contain_card_in_details_mark_to_batch_view.dart';

class CustomCardInDetailsMarkToBatchView extends StatelessWidget {
  const CustomCardInDetailsMarkToBatchView({
    super.key,
    required this.examsResultToBatchModel,
  });
  final ExamsResultToBatchModel examsResultToBatchModel;
  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 0,
      margin: OnlyPaddingWithoutChild.left18AndRight22AndBottom8(
        context: context,
      ),
      shape: RoundedRectangleBorder(
        borderRadius: Circulars.circular10(context: context),
      ),
      color: ColorsStyle.whiteColor,
      child: CustomContainCardInDetailsMarkToBatchView(
        examsResultToBatchModel: examsResultToBatchModel,
      ),
    );
  }
}
