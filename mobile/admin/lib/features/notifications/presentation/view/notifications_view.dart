import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/notifications/presentation/view/widgets/custom_notificaitons_view_body.dart';

class NotificationsView extends StatelessWidget {
  const NotificationsView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomNotificaitonsViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(
        body: CustomNotificaitonsViewBody(),
      );
    }
  }
}
