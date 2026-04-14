import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_exams_to_student_view_body.dart';

class ExamsToStudentView extends StatelessWidget {
  const ExamsToStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomExamsToStudentViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(
        body: CustomExamsToStudentViewBody(),
      );
    }
  }
}
