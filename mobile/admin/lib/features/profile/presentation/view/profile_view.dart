import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/profile/presentation/view/widgets/custom_profile_view_body.dart';

class ProfileView extends StatelessWidget {
  const ProfileView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomProfileViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(body: CustomProfileViewBody());
    }
  }
}
