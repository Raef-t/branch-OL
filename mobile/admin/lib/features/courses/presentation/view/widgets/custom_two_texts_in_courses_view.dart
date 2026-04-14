import 'package:flutter/material.dart';
import '/core/components/text_medium13_component.dart';
import '/core/components/text_medium16_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomTwoTextsInCoursesView extends StatelessWidget {
  const CustomTwoTextsInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left48AndRight17(
      context: context,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          const TextMedium16Component(
            text: 'الأقسام',
            fontFamily: FontFamily.tajawal,
            color: ColorsStyle.mediumBlackColor2,
          ),
          Heights.height11(context: context),
          const TextMedium13Component(
            text: 'إجمالي عدد الطلاب في جميع الدورات الدراسية لهذا العام',
            color: ColorsStyle.greyColor,
            fontFamily: FontFamily.tajawal,
          ),
        ],
      ),
    );
  }
}
