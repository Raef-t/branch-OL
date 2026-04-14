import 'package:flutter/material.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/features/class/presentation/view/widgets/custom_app_bar_widget_in_class_view.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';

class CustomSliverAppBarInClassView extends StatelessWidget {
  const CustomSliverAppBarInClassView({super.key, required this.batchesModel});
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return SliverAppBarToHoleAppComponent(
      appBarWidget: CustomAppBarWidgetInClassView(batchesModel: batchesModel),
    );
  }
}
