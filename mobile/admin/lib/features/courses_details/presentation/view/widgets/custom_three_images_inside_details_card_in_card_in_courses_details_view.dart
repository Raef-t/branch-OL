import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_teacher_image_inside_details_card_in_courses_details_view.dart';
import '/gen/assets.gen.dart';

class CustomThreeImagesInsideDetailsCardInCardInCoursesDetailsView
    extends StatelessWidget {
  const CustomThreeImagesInsideDetailsCardInCardInCoursesDetailsView({
    super.key,
    required this.batchesModel,
  });
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Assets.images.watchImage.image(),
        Heights.height15(context: context),
        Assets.images.pinkWorldImage.image(),
        Heights.height15(context: context),
        CustomTeacherImageInsideDetailsCardInCoursesDetailsView(
          batchesModel: batchesModel,
        ),
      ],
    );
  }
}
