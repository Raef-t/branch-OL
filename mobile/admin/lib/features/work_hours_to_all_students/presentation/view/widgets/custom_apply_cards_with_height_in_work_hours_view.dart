import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_apply_tab_view_in_work_hours_view.dart';

class CustomApplyCardsWithHeightInWorkHoursView extends StatelessWidget {
  const CustomApplyCardsWithHeightInWorkHoursView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Heights.height58(context: context),
        const CustomApplyTabViewInWorkHoursView(),
      ],
    );
  }
}
