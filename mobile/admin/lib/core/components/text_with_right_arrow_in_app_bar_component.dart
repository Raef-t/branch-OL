import 'package:flutter/material.dart';
import '/core/components/right_arrow_image_in_sliver_app_bar_with_click_component.dart';
import '/core/components/text_medium18_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class TextWithRightArrowInAppBarComponent extends StatelessWidget {
  const TextWithRightArrowInAppBarComponent({
    super.key,
    required this.text,
    this.image,
  });
  final String text;
  final Image? image;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right25(
      context: context,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          TextMedium18Component(
            text: text,
            fontFamily: FontFamily.tajawal,
            color: ColorsStyle.mediumBrownColor,
          ),
          Widths.width10(context: context),
          RightArrowImageInSliverAppBarWithClickComponent(
            image:
                image ??
                Assets.images.rightArrowWithoutLineInCenterItImage.image(),
          ),
        ],
      ),
    );
  }
}
