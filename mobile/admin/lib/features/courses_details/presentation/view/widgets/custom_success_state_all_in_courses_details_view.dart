import 'package:flutter/material.dart';
import '/core/lists/colors_to_details_card_in_courses_details_view_list.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_details_card_in_courses_details_view.dart';

class CustomSuccessStateAllInCoursesDetailsView extends StatelessWidget {
  const CustomSuccessStateAllInCoursesDetailsView({
    super.key,
    required this.lengthToListOfBatchesModel,
    this.listOfBatchesModel,
  });
  final int lengthToListOfBatchesModel;
  final List<BatchesCoursesDetailsModel>? listOfBatchesModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(lengthToListOfBatchesModel, (index) {
        final batchesModel = listOfBatchesModel?[index];
        final color =
            colorsToDetailsCardInCoursesDetailsViewList[index %
                colorsToDetailsCardInCoursesDetailsViewList.length];
        return CustomDetailsCardInCoursesDetailsView(
          circleColor: color,
          verticalLineColor: color,
          batchesModel: batchesModel!,
        );
      }),
    );
  }
}
