import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/notifications/presentation/view/widgets/custom_contain_notification_card_in_notifications_view.dart';

class CustomNotificationCardInNotificationsView extends StatefulWidget {
  const CustomNotificationCardInNotificationsView({super.key});
  @override
  State<CustomNotificationCardInNotificationsView> createState() =>
      _CustomNotificationCardInNotificationsViewState();
}

class _CustomNotificationCardInNotificationsViewState
    extends State<CustomNotificationCardInNotificationsView> {
  bool isReaded = false;
  void readedOnPressed() {
    setState(() => isReaded = true);
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: OnlyPaddingWithoutChild.left26AndRight44AndTop10AndBottom11(
        context: context,
      ),
      margin: OnlyPaddingWithoutChild.bottom21(context: context),
      decoration:
          BoxDecorations.boxDecorationToNotificationCardInNotificationsView(
            isReaded: isReaded,
          ),
      child: CustomContainNotificationCardInNotificationsView(
        readedOnPressed: readedOnPressed,
      ),
    );
  }
}
