import 'package:flutter/material.dart';
import '/core/components/divider_without_fixed_size_component.dart';
import '/core/lists/times_in_apply_tab_view_in_work_hours_view_list.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_two_texts_in_apply_tab_view_in_work_hours_view.dart';

class CustomColumnTimesWithDividerInApplyTabViewInWorkHoursView
    extends StatelessWidget {
  const CustomColumnTimesWithDividerInApplyTabViewInWorkHoursView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(timesInApplyTabViewInWorkHoursViewList.length, (
        index,
      ) {
        final text = timesInApplyTabViewInWorkHoursViewList[index];
        return Column(
          children: [
            CustomTwoTextsInApplyTabViewInWorkHoursView(text: text),
            const DividerWithoutFixedSizeComponent(),
          ],
        );
      }),
    );
  }
}
