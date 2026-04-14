import 'dart:io';
import 'package:flutter/cupertino.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/teachers/presentation/view/widgets/custom_teachers_view_body.dart';

class TeachersView extends StatelessWidget {
  const TeachersView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomTeachersViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(body: CustomTeachersViewBody());
    }
  }
}
