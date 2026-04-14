import 'package:flutter/material.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_sliver_app_bar_in_details_mark_to_batch_view.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_sliver_fill_remaining_in_details_mark_to_batch_view.dart';

class CustomDetailsMarkToBatchViewBody extends StatelessWidget {
  const CustomDetailsMarkToBatchViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return const CustomScrollView(
      slivers: [
        const CustomSliverAppBarInDetailsMarkToBatchView(),
        CustomSliverFillRemainingInDetailsMarkToBatchView(),
      ],
    );
  }
}
