import 'package:flutter/cupertino.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';

class CustomSliverAppBarWidgetInAttendaceView extends StatelessWidget {
  const CustomSliverAppBarWidgetInAttendaceView({super.key});

  @override
  Widget build(BuildContext context) {
    return const SliverAppBarToHoleAppComponent(
      appBarWidget: AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
        firstText: 'الغياب و الحضور',
        secondText: 'يمكنك الاطلاع على حالة التفقد',
        thirdText: 'للطالب',
      ),
    );
  }
}
