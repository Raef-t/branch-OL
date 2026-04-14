import 'dart:io';
import 'package:flutter/cupertino.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/attendance/presentation/view/widgets/custom_attendance_view_body.dart';

class AttendanceView extends StatelessWidget {
  const AttendanceView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomAttendanceViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(body: CustomAttendanceViewBody());
    }
  }
}
