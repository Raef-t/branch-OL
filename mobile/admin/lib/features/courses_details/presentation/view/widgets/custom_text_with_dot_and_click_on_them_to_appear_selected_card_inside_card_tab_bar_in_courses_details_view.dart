import 'package:flutter/material.dart';
import '/features/courses_details/presentation/view/widgets/custom_selected_card_to_card_tab_bar_in_courses_details_view.dart';
import '/features/courses_details/presentation/view/widgets/custom_text_with_dot_inside_card_tab_bar_in_courses_details_view.dart';

class CustomTextWithDotAndClickOnThemToAppearSelectedCardInsideCardTabBarInCoursesDetailsView
    extends StatelessWidget {
  const CustomTextWithDotAndClickOnThemToAppearSelectedCardInsideCardTabBarInCoursesDetailsView({
    super.key,
    required this.selectedIndex,
    required this.index,
    required this.onTapSelected,
    required this.text,
  });
  final int selectedIndex, index;
  final ValueChanged<int> onTapSelected;
  final String text;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => onTapSelected(index),
      child: CustomSelectedCardToCardTabBarInCoursesDetailsView(
        selectedIndex: selectedIndex,
        index: index,
        child: CustomTextWithDotInsideCardTabBarInCoursesDetailsView(
          text: text,
          index: index,
          selectedIndex: selectedIndex,
        ),
      ),
    );
  }
}
