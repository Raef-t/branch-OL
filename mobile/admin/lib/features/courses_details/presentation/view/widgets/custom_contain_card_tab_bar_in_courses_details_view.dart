import 'package:flutter/material.dart';
import '/features/courses_details/presentation/view/widgets/custom_text_and_click_on_it_to_appear_selected_card_inside_card_tab_bar_in_courses_details_view.dart';
import '/features/courses_details/presentation/view/widgets/custom_text_with_dot_and_click_on_them_to_appear_selected_card_inside_card_tab_bar_in_courses_details_view.dart';

class CustomContainCardTabBarInCoursesDetailsView extends StatelessWidget {
  const CustomContainCardTabBarInCoursesDetailsView({
    super.key,
    required this.selectedIndex,
    required this.onTapSelected,
  });
  final int selectedIndex;
  final ValueChanged<int> onTapSelected;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceAround,
      children: [
        CustomTextWithDotAndClickOnThemToAppearSelectedCardInsideCardTabBarInCoursesDetailsView(
          selectedIndex: selectedIndex,
          index: 0,
          onTapSelected: onTapSelected,
          text: 'بنات',
        ),
        CustomTextWithDotAndClickOnThemToAppearSelectedCardInsideCardTabBarInCoursesDetailsView(
          selectedIndex: selectedIndex,
          index: 1,
          onTapSelected: onTapSelected,
          text: 'شباب',
        ),
        CustomTextAndClickOnItToAppearSelectedCardInsideCardTabBarInCoursesDetailsView(
          selectedIndex: selectedIndex,
          onTapSelected: onTapSelected,
        ),
      ],
    );
  }
}
