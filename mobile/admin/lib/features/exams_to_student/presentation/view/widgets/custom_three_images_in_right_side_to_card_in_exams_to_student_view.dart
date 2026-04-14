import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/gen/assets.gen.dart';

class CustomThreeImagesInRightSideToCardInExamsToStudentView
    extends StatelessWidget {
  const CustomThreeImagesInRightSideToCardInExamsToStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Assets.images.dateImage.image(),
        Heights.height15(context: context),
        Assets.images.grayWorldImage.image(),
        Heights.height15(context: context),
        Assets.images.locationUpCircleDeterminedImage.image(),
      ],
    );
  }
}
