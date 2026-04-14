import 'package:flutter/material.dart';
import 'package:second_page_app/core/components/circle_q_r_in_bottom_navigation_bar_component.dart';
import '/core/components/icon_and_label_in_bottom_navigation_bar_to_many_views_component.dart';
import '/gen/assets.gen.dart';

class ContainBottomNavigationBarCardToManyViewComponent
    extends StatelessWidget {
  const ContainBottomNavigationBarCardToManyViewComponent({
    super.key,
    required this.currentIndex,
    required this.onTap,
  });

  final int currentIndex;
  final void Function(int) onTap;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: IconAndLabelInBottomNavigationBarToManyViewsComponent(
            imagePath: Assets.images.homeImage,
            text: 'الرئيسية',
            index: 0,
            currentIndex: currentIndex,
            onTap: onTap,
          ),
        ),
        Expanded(
          child: IconAndLabelInBottomNavigationBarToManyViewsComponent(
            imagePath: Assets.images.coursesImage,
            text: 'الدورات',
            index: 1,
            currentIndex: currentIndex,
            onTap: onTap,
          ),
        ),
        const Expanded(child: CircleQRInBottomNavigationBarComponent()),
        Expanded(
          child: IconAndLabelInBottomNavigationBarToManyViewsComponent(
            imagePath: Assets.images.teachersImage,
            text: 'المدرسون',
            index: 2,
            currentIndex: currentIndex,
            onTap: onTap,
          ),
        ),
        Expanded(
          child: IconAndLabelInBottomNavigationBarToManyViewsComponent(
            imagePath: Assets.images.profilesImage,
            text: 'بروفايل',
            index: 3,
            currentIndex: currentIndex,
            onTap: onTap,
          ),
        ),
      ],
    );
  }
}
