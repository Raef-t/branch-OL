import 'package:flutter/material.dart';
import '/features/home/presentation/view/widgets/custom_actions_app_bar_in_home_view.dart';
import '/features/home/presentation/view/widgets/custom_leading_app_bar_in_home_view.dart';

class CustomAppBarWidgetInHomeView extends StatelessWidget {
  const CustomAppBarWidgetInHomeView({
    super.key,
    required this.userName,
    required this.userPhoto,
  });
  final String userName, userPhoto;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        const CustomLeadingAppBarInHomeView(),
        const Spacer(),
        CustomActionsAppBarInHomeView(userName: userName, userPhoto: userPhoto),
      ],
    );
  }
}
