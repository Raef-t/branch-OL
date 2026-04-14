import 'package:flutter/material.dart';
import '/features/notifications/presentation/view/widgets/custom_notification_card_in_notifications_view.dart';

class CustomGenerateNotificationCardInNotificationsView
    extends StatelessWidget {
  const CustomGenerateNotificationCardInNotificationsView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(4, (index) {
        return const CustomNotificationCardInNotificationsView();
      }),
    );
  }
}
