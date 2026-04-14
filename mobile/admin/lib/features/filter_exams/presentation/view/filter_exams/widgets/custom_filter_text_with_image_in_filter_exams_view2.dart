import 'package:flutter/material.dart';
import '/core/components/text_bold16_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/gen/assets.gen.dart';

class CustomFilterTextWithImageInFilterExamsView2 extends StatelessWidget {
  const CustomFilterTextWithImageInFilterExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right23(
      context: context,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          const TextBold16Component(text: 'فلترة'),
          Widths.width13(context: context),
          Assets.images.rightArrowWithoutLineInCenterItImage.image(),
        ],
      ),
    );
  }
}
