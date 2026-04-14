import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/all_padding_without_child.dart';

class CustomSelectedCardToCardTabBarInCoursesDetailsView
    extends StatelessWidget {
  const CustomSelectedCardToCardTabBarInCoursesDetailsView({
    super.key,
    required this.selectedIndex,
    required this.index,
    required this.child,
  });
  final int selectedIndex, index;
  final Widget child;
  @override
  Widget build(BuildContext context) {
    double width = MediaQuery.sizeOf(context).width;
    return Container(
      width: width * 0.26,
      alignment: Alignment.center,
      padding: AllPaddingWithoutChild.all10(context: context),
      decoration: selectedIndex == index
          ? BoxDecorations.boxDecorationToSelectedCardToCardTabBarInCoursesDetailsView(
              context: context,
            )
          : null,
      child: child,
    );
  }
}
