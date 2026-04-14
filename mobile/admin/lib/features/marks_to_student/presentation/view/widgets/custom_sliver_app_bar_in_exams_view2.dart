import 'package:flutter/material.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_app_bar_widget_in_exams_view2.dart';

class CustomSliverAppBarInExamsView2 extends StatelessWidget {
  const CustomSliverAppBarInExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return const SliverAppBarToHoleAppComponent(
      appBarWidget: CustomAppBarWidgetInExamsView2(),
    );
  }
}
