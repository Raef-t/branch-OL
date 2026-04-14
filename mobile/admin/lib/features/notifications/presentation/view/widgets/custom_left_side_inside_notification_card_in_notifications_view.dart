import 'package:flutter/material.dart';
import '/features/notifications/presentation/view/widgets/custom_vertical_menu_card_in_notifications_view.dart';
import '/features/notifications/presentation/view/widgets/custom_vertical_menu_image_in_notifications_view.dart';

class CustomLeftSideInsideNotificationCardInNotificationsView
    extends StatefulWidget {
  const CustomLeftSideInsideNotificationCardInNotificationsView({
    super.key,
    required this.readedOnPressed,
  });
  final VoidCallback readedOnPressed;
  @override
  State<CustomLeftSideInsideNotificationCardInNotificationsView>
  createState() =>
      _CustomLeftSideInsideNotificationCardInNotificationsViewState();
}

class _CustomLeftSideInsideNotificationCardInNotificationsViewState
    extends State<CustomLeftSideInsideNotificationCardInNotificationsView> {
  bool isShowingTheContainer = false;
  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        CustomVerticalMenuImageInNotificationsView(
          onTap: () => setState(() => isShowingTheContainer = true),
        ),
        if (isShowingTheContainer)
          CustomVerticalMenuCardInNotificationsView(
            readedOnPressed: () {
              widget.readedOnPressed();
              setState(() => isShowingTheContainer = false);
            },
            deleteOnPressed: () =>
                setState(() => isShowingTheContainer = false),
          ),
      ],
    );
  }
}
