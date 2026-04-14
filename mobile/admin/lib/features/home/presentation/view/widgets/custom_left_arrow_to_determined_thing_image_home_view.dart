import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/gen/assets.gen.dart';

class CustomLeftArrowToDeterminedThingImageHomeView extends StatelessWidget {
  const CustomLeftArrowToDeterminedThingImageHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.top15(
      context: context,
      child: Assets.images.leftArrowToDeterminedThingImage.image(),
    );
  }
}
