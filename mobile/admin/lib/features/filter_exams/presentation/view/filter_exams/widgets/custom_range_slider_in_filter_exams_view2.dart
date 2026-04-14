import 'package:flutter/material.dart';
import '/core/helpers/build_range_labels_in_filter_exams_view2_helper.dart';

class CustomRangeSliderInFilterExamsView2 extends StatelessWidget {
  const CustomRangeSliderInFilterExamsView2({
    super.key,
    required this.rangeValues,
    required this.onChanged,
  });
  final RangeValues rangeValues;
  final void Function(RangeValues) onChanged;
  @override
  Widget build(BuildContext context) {
    return RangeSlider(
      min: 0, //smallest value the RangeSlider can reach
      max: 600, //largest value the RangeSlider can reach
      values: rangeValues,
      //you give newValues to values attribute because you update the values from your fingers and onChanged method do that the update
      divisions: 600,
      //it's mean how many steps that will walked them, and you know from this operation((max - min)/division) so (600-0)/60 = 10 so will walk 10 step in all one step
      onChanged: onChanged,
      labels: buildRangeLabelsInFilterExamsView2Helper(
        rangeValues: rangeValues,
      ),
    );
  }
}
