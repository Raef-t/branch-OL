import 'package:flutter/material.dart';
import '/features/notifications/presentation/view/widgets/custom_sliver_app_bar_in_notifications_view.dart';
import '/features/notifications/presentation/view/widgets/custom_sliver_fill_remaining_in_notifications_view.dart';

class CustomNotificaitonsViewBody extends StatelessWidget {
  const CustomNotificaitonsViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return const CustomScrollView(
      slivers: [
        CustomSliverAppBarInNotificationsView(),
        CustomSliverFillRemainingInNotificationsView(),
      ],
    );
  }
}
