import 'dart:io';
import 'package:flutter/cupertino.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/work_hours_to_student/presentation/view/widgets/custom_work_hours_to_student_view_body.dart';

class WorkHoursToStudentView extends StatelessWidget {
  const WorkHoursToStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomWorkHoursToStudentViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(
        body: CustomWorkHoursToStudentViewBody(),
      );
    }
  }
}
