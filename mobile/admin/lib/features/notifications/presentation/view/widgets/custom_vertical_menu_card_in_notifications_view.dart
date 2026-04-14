import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/features/notifications/presentation/view/widgets/custom_contain_vertical_menu_card_in_notifications_view.dart';

class CustomVerticalMenuCardInNotificationsView extends StatelessWidget {
  const CustomVerticalMenuCardInNotificationsView({
    super.key,
    required this.readedOnPressed,
    required this.deleteOnPressed,
  });
  final void Function() readedOnPressed, deleteOnPressed;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration:
          BoxDecorations.boxDecorationToVerticalMenuCardInNotificationsView(
            context: context,
          ),
      child: CustomContainVerticalMenuCardInNotificationsView(
        readedOnPressed: readedOnPressed,
        deleteOnPressed: deleteOnPressed,
      ),
    );
  }
}
