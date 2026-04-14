import 'package:flutter/material.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/gen/assets.gen.dart';

class CustomSliverAppBarInCoursesView extends StatelessWidget {
  const CustomSliverAppBarInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return SliverAppBarToHoleAppComponent(
      appBarWidget: AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
        firstText: 'الدورات',
        secondText: 'يمكنك الاطلاع على جميع الدورات',
        thirdText: 'لهذه الفرع',
        image: Assets.images.rightArrowWithLineInCenterItImage.image(),
      ),
    );
  }
}
