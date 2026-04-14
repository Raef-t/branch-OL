import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_exams_view_body2.dart';

class ExamsView2 extends StatelessWidget {
  const ExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomExamsViewBody2(),
      );
    } else {
      return const ScaffoldWithBodyComponent(body: CustomExamsViewBody2());
    }
  }
}
