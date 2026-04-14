import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/styles/colors_style.dart';
import '/features/courses_details/presentation/view/widgets/custom_selected_card_to_card_tab_bar_in_courses_details_view.dart';
import '/gen/fonts.gen.dart';

class CustomTextAndClickOnItToAppearSelectedCardInsideCardTabBarInCoursesDetailsView
    extends StatelessWidget {
  const CustomTextAndClickOnItToAppearSelectedCardInsideCardTabBarInCoursesDetailsView({
    super.key,
    required this.selectedIndex,
    required this.onTapSelected,
  });
  final int selectedIndex;
  final ValueChanged<int> onTapSelected;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => onTapSelected(2),
      child: CustomSelectedCardToCardTabBarInCoursesDetailsView(
        selectedIndex: selectedIndex,
        index: 2,
        child: TextMedium16Component(
          text: 'الكل',
          fontFamily: FontFamily.tajawal,
          color: selectedIndex == 2
              ? ColorsStyle.littleGreyColor
              : ColorsStyle.greyColor,
        ),
      ),
    );
  }
}
