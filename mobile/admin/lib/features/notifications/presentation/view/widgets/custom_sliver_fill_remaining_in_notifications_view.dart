import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/notifications/presentation/view/widgets/custom_generate_notification_card_in_notifications_view.dart';

class CustomSliverFillRemainingInNotificationsView extends StatelessWidget {
  const CustomSliverFillRemainingInNotificationsView({super.key});

  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height24(context: context),
            const CustomGenerateNotificationCardInNotificationsView(),
          ],
        ),
      ),
    );
  }
}
