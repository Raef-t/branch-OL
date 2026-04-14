import 'package:flutter/material.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';

class CustomSliverAppBarInNotificationsView extends StatelessWidget {
  const CustomSliverAppBarInNotificationsView({super.key});

  @override
  Widget build(BuildContext context) {
    return const SliverAppBarToHoleAppComponent(
      appBarWidget: AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
        firstText: 'الإشعارات',
        secondText: 'يمكنك الاطلاع على الإشعارات الخاصة',
        thirdText: 'بالطالب',
      ),
    );
  }
}
