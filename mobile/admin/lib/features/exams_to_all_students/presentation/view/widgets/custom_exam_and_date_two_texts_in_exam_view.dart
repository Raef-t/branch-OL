import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';

class CustomExamAndDateTwoTextsInExamView extends StatelessWidget {
  const CustomExamAndDateTwoTextsInExamView({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right28(
      context: context,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          const TextMedium14Component(
            text: 'المذاكرة',
            color: ColorsStyle.mediumBrownColor,
          ),
          Widths.width56(context: context),
          const TextMedium14Component(
            text: 'الوقت',
            color: ColorsStyle.mediumBrownColor,
          ),
        ],
      ),
    );
  }
}
