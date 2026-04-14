import 'package:flutter/material.dart';
import '/core/components/card_contain_on_image_component.dart';
import '/core/components/two_texts_in_the_bottom_card_contain_on_image_component.dart';
import '/core/sized_boxs/heights.dart';

class CardAndTwoTextsComponent extends StatelessWidget {
  const CardAndTwoTextsComponent({
    super.key,
    required this.firstText,
    required this.secondText,
    required this.imageProvider,
  });
  final String firstText, secondText;
  final ImageProvider imageProvider;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        CardContainOnImageComponent(imageProvider: imageProvider),
        Heights.height8(context: context),
        TwoTextsInTheBottomCardContainOnImageComponent(
          firstText: firstText,
          secondText: secondText,
        ),
      ],
    );
  }
}
