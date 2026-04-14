import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_range_slider_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_theme_widget_to_range_slider_in_filter_exams_view2.dart';

class CustomRangeSliderWithThemeWidgetInFilterExamsView2
    extends StatelessWidget {
  const CustomRangeSliderWithThemeWidgetInFilterExamsView2({
    super.key,
    required this.rangeValues,
    required this.onChanged,
  });
  final RangeValues rangeValues;
  final void Function(RangeValues) onChanged;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left20AndRight5(
      context: context,
      child: CustomThemeWidgetToRangeSliderInFilterExamsView2(
        child: CustomRangeSliderInFilterExamsView2(
          rangeValues: rangeValues,
          onChanged: onChanged,
        ),
      ),
    );
  }
}
