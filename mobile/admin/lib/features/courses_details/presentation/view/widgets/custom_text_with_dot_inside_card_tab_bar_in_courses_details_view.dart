import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/courses_details/presentation/view/widgets/custom_dot_inside_card_tab_bar_in_courses_details_view.dart';
import '/gen/fonts.gen.dart';

class CustomTextWithDotInsideCardTabBarInCoursesDetailsView
    extends StatelessWidget {
  const CustomTextWithDotInsideCardTabBarInCoursesDetailsView({
    super.key,
    required this.text,
    required this.index,
    required this.selectedIndex,
  });
  final String text;
  final int index, selectedIndex;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        TextMedium16Component(
          text: text,
          fontFamily: FontFamily.tajawal,
          color: selectedIndex == index
              ? ColorsStyle.littleGreyColor
              : ColorsStyle.greyColor,
        ),
        text == 'بنات'
            ? Widths.width10(context: context)
            : Widths.width5(context: context),
        CustomDotInsideCardTabBarInCoursesDetailsView(
          color: text == 'بنات'
              ? ColorsStyle.mediumPinkColor
              : ColorsStyle.deepBlueColor,
        ),
      ],
    );
  }
}
