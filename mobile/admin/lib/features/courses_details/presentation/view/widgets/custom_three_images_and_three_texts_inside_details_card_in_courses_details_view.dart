import 'package:flutter/material.dart';
import '/core/sized_boxs/widths.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_three_images_inside_details_card_in_card_in_courses_details_view.dart';
import '/features/courses_details/presentation/view/widgets/custom_three_texts_inside_details_card_in_card_in_courses_details_view.dart';

class CustomThreeImagesAndThreeTextsInsideDetailsCardInCoursesDetailsView
    extends StatelessWidget {
  const CustomThreeImagesAndThreeTextsInsideDetailsCardInCoursesDetailsView({
    super.key,
    required this.batchesModel,
  });
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CustomThreeTextsInsideDetailsCardInCardInCoursesDetailsView(
          batchesModel: batchesModel,
        ),
        Widths.width13(context: context),
        CustomThreeImagesInsideDetailsCardInCardInCoursesDetailsView(
          batchesModel: batchesModel,
        ),
      ],
    );
  }
}
