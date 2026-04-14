import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/work_hours_to_batch/presentation/view/widgets/custom_work_hours_to_batch_view_body.dart';

class WorkHoursToBatchView extends StatelessWidget {
  const WorkHoursToBatchView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomWorkHoursToBatchViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(
        body: CustomWorkHoursToBatchViewBody(),
      );
    }
  }
}
