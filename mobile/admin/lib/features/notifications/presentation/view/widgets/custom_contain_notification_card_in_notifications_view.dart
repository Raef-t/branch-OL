import 'package:flutter/material.dart';
import '/features/notifications/presentation/view/widgets/custom_left_side_inside_notification_card_in_notifications_view.dart';
import '/features/notifications/presentation/view/widgets/custom_right_side_inside_notification_card_in_notifications_view.dart';

class CustomContainNotificationCardInNotificationsView extends StatelessWidget {
  const CustomContainNotificationCardInNotificationsView({
    super.key,
    required this.readedOnPressed,
  });
  final VoidCallback readedOnPressed;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CustomLeftSideInsideNotificationCardInNotificationsView(
          readedOnPressed: readedOnPressed,
        ),
        const Spacer(),
        const CustomRightSideInsideNotificationCardInNotificationsView(),
      ],
    );
  }
}
