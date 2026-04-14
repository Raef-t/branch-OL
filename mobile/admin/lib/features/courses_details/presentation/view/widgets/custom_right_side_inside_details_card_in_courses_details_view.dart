import 'package:flutter/material.dart';
import '/core/components/vertical_line_that_clipper_from_top_left_and_bottom_left_component.dart';
import '/core/sized_boxs/widths.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_all_texts_and_images_inside_details_card_in_courses_details_view.dart';

class CustomRightSideInsideDetailsCardInCoursesDetailsView
    extends StatelessWidget {
  const CustomRightSideInsideDetailsCardInCoursesDetailsView({
    super.key,
    required this.verticalLineColor,
    required this.batchesModel,
  });
  final Color verticalLineColor;
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CustomAllTextsAndImagesInsideDetailsCardInCoursesDetailsView(
          batchesModel: batchesModel,
        ),
        Widths.width12(context: context),
        VerticalLineThatClipperFromTopLeftAndBottomLeftComponent(
          color: verticalLineColor,
        ),
      ],
    );
  }
}
