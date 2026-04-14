import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_three_images_and_three_texts_inside_details_card_in_courses_details_view.dart';
import '/gen/fonts.gen.dart';

class CustomAllTextsAndImagesInsideDetailsCardInCoursesDetailsView
    extends StatelessWidget {
  const CustomAllTextsAndImagesInsideDetailsCardInCoursesDetailsView({
    super.key,
    required this.batchesModel,
  });
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        TextMedium16Component(
          text: batchesModel.batchName ?? 'لا يوجد شعبه',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBlackColor2,
        ),
        Heights.height8(context: context),
        CustomThreeImagesAndThreeTextsInsideDetailsCardInCoursesDetailsView(
          batchesModel: batchesModel,
        ),
      ],
    );
  }
}
