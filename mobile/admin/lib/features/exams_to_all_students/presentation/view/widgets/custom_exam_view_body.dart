import 'package:flutter/material.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_sliver_app_bar_to_exam_view.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_sliver_fill_remaining_to_exam_view.dart';

class CustomExamViewBody extends StatelessWidget {
  const CustomExamViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return const CustomScrollView(
      slivers: [
        CustomSliverAppBarToExamView(),
        CustomSliverFillRemainingToExamView(),
      ],
    );
  }
}
