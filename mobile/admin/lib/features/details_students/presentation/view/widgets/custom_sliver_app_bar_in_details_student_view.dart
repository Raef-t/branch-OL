import 'package:flutter/material.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/features/details_students/presentation/view/widgets/custom_app_bar_widget_in_details_student_view.dart';

class CustomSliverAppBarInDetailsStudentView extends StatelessWidget {
  const CustomSliverAppBarInDetailsStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    return const SliverAppBarToHoleAppComponent(
      appBarWidget: CustomAppBarWidgetInDetailsStudentView(),
    );
  }
}
