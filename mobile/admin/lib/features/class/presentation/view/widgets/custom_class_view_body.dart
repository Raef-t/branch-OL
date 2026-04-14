import 'package:flutter/material.dart';
import '/features/class/presentation/view/widgets/custom_sliver_app_bar_in_class_view.dart';
import '/features/class/presentation/view/widgets/custom_sliver_fill_remaining_in_class_view.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';

class CustomClassViewBody extends StatelessWidget {
  const CustomClassViewBody({super.key, required this.batchesModel});
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        CustomSliverAppBarInClassView(batchesModel: batchesModel),
        const CustomSliverFillRemainingInClassView(),
      ],
    );
  }
}
