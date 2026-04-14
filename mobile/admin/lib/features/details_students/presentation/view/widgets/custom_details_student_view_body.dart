import 'package:flutter/material.dart';
import '/features/details_students/presentation/view/widgets/custom_sliver_app_bar_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_sliver_fill_remaining_in_details_student_view.dart';

class CustomDetailsStudentViewBody extends StatefulWidget {
  const CustomDetailsStudentViewBody({super.key});

  @override
  State<CustomDetailsStudentViewBody> createState() =>
      _CustomDetailsStudentViewBodyState();
}

class _CustomDetailsStudentViewBodyState
    extends State<CustomDetailsStudentViewBody> {
  String selectedValue = 'شهر';
  double maxRating = 100;
  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        const CustomSliverAppBarInDetailsStudentView(),
        CustomSliverFillRemainingInDetailsStudentView(
          selectedValue: selectedValue,
          maxRating: maxRating,
          onSelected: (value) => setState(() => selectedValue = value),
        ),
      ],
    );
  }
}
