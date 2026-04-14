import 'package:flutter/material.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_two_texts_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';

class CustomSliverAppBarInSearchView extends StatelessWidget {
  const CustomSliverAppBarInSearchView({super.key});

  @override
  Widget build(BuildContext context) {
    return const SliverAppBarToHoleAppComponent(
      appBarWidget: AppBarWidgetWithRightArrowImageAndTwoTextsComponent(
        firstText: 'البحث',
        secondText: 'يمكنك الاطلاع على برنامج دوام الطالب',
      ),
    );
  }
}
