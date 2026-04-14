import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/gen/assets.gen.dart';

class CustomTwoImagesDetailsAboutExamInExamView extends StatelessWidget {
  const CustomTwoImagesDetailsAboutExamInExamView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Assets.images.likeStarInsideCircleImage.image(),
        Heights.height5(context: context),
        Assets.images.locationUpCircleDeterminedImage.image(),
      ],
    );
  }
}
