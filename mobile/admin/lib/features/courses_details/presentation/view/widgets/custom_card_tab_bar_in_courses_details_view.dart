import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/courses_details/presentation/view/widgets/custom_contain_card_tab_bar_in_courses_details_view.dart';

class CustomCardTabBarInCoursesDetailsView extends StatelessWidget {
  const CustomCardTabBarInCoursesDetailsView({
    super.key,
    required this.selectedIndex,
    required this.onTapSelected,
  });
  final int selectedIndex;
  final ValueChanged<int> onTapSelected;
  @override
  Widget build(BuildContext context) {
    return Container(
      margin: OnlyPaddingWithoutChild.left40AndRight39AndBottom15(
        context: context,
      ),
      // padding: OnlyPaddingWithoutChild.left11AndTop8AndBottom7(
      //   context: context,
      // ),
      decoration: BoxDecorations.boxDecorationToCardTabBarInCoursesDetailsView(
        context: context,
      ),
      child: CustomContainCardTabBarInCoursesDetailsView(
        selectedIndex: selectedIndex,
        onTapSelected: onTapSelected,
      ),
    );
  }
}
