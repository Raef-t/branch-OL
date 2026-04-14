import 'package:flutter/material.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/gen/assets.gen.dart';

class CustomSliverAppBarInCoursesDetailsView extends StatelessWidget {
  const CustomSliverAppBarInCoursesDetailsView({
    super.key,
    required this.appBarCourseName,
  });
  final String appBarCourseName;
  @override
  Widget build(BuildContext context) {
    return SliverAppBarToHoleAppComponent(
      appBarWidget: AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
        firstText: appBarCourseName,
        secondText: 'يمكنك الاطلاع على جميع الشعب في',
        thirdText: 'هذه الدورة',
        image: Assets.images.rightArrowWithLineInCenterItImage.image(),
      ),
    );
  }
}
