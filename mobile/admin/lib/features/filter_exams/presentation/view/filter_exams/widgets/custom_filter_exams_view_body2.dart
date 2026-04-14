import 'package:flutter/material.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_sliver_app_bar_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_sliver_fill_remaining_in_filter_exams_view2.dart';

class CustomFilterExamsViewBody2 extends StatefulWidget {
  const CustomFilterExamsViewBody2({super.key});

  @override
  State<CustomFilterExamsViewBody2> createState() =>
      _CustomFilterExamsViewBody2State();
}

class _CustomFilterExamsViewBody2State
    extends State<CustomFilterExamsViewBody2> {
  RangeValues rangeValues = const RangeValues(0, 600);
  //left is 0 so the start in RangeSlider is 0, right is 600 so the end in RangeSlider is 600
  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        const CustomSliverAppBarInFilterExamsView2(),
        CustomSliverFillRemainingInFilterExamsView2(
          rangeValues: rangeValues,
          onChanged: (newValues) => setState(() => rangeValues = newValues),
        ),
      ],
    );
  }
}
