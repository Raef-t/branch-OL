import 'package:flutter/material.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_sliver_app_bar_in_exams_view2.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_sliver_fill_remaining_in_exams_view2.dart';

class CustomExamsViewBody2 extends StatelessWidget {
  const CustomExamsViewBody2({super.key});

  @override
  Widget build(BuildContext context) {
    return const CustomScrollView(
      slivers: [
        CustomSliverAppBarInExamsView2(),
        CustomSliverFillRemainingInExamsView2(),
      ],
    );
  }
}
