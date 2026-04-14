import 'package:flutter/material.dart';
import '/core/components/text_with_28right_padding_in_app_bar_component.dart';
import '/core/components/text_with_right_arrow_in_app_bar_component.dart';
import '/core/sized_boxs/heights.dart';

class AppBarWidgetWithRightArrowImageAndThreeTextsComponent
    extends StatelessWidget {
  const AppBarWidgetWithRightArrowImageAndThreeTextsComponent({
    super.key,
    required this.firstText,
    required this.secondText,
    required this.thirdText,
    this.image,
  });
  final String firstText, secondText, thirdText;
  final Image? image;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Heights.height58(context: context),
        TextWithRightArrowInAppBarComponent(text: firstText, image: image),
        Heights.height13(context: context),
        TextWith28RightPaddingInAppBarComponent(text: secondText),
        TextWith28RightPaddingInAppBarComponent(text: thirdText),
      ],
    );
  }
}
