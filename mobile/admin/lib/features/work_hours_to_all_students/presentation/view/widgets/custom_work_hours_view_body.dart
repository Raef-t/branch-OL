import 'package:flutter/material.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_sliver_app_bar_in_work_hours_view.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_sliver_fill_remaining_in_work_hours_view.dart';

class CustomWorkHoursViewBody extends StatelessWidget {
  const CustomWorkHoursViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return const CustomScrollView(
      slivers: [
        CustomSliverAppBarInWorkHoursView(),
        CustomSliverFillRemainingInWorkHoursView(),
      ],
    );
  }
}
