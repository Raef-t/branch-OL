import 'package:flutter/material.dart';
import '/core/components/text_medium10_component.dart';
import '/gen/fonts.gen.dart';

class TwoTextsInTheBottomCardContainOnImageComponent extends StatelessWidget {
  const TwoTextsInTheBottomCardContainOnImageComponent({
    super.key,
    required this.firstText,
    required this.secondText,
  });
  final String firstText, secondText;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        TextMedium10Component(text: firstText, fontFamily: FontFamily.tajawal),
        TextMedium10Component(text: secondText, fontFamily: FontFamily.tajawal),
      ],
    );
  }
}
