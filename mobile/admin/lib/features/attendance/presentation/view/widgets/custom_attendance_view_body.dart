import 'package:flutter/cupertino.dart';
import '/features/attendance/presentation/view/widgets/custom_sliver_app_bar_widget_in_attendace_view.dart';
import '/features/attendance/presentation/view/widgets/custom_sliver_fill_remaining_in_attendace_view.dart';

class CustomAttendanceViewBody extends StatelessWidget {
  const CustomAttendanceViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return const CustomScrollView(
      slivers: [
        CustomSliverAppBarWidgetInAttendaceView(),
        CustomSliverFillRemainingInAttendaceView(),
      ],
    );
  }
}
