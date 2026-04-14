import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_exam_view_body.dart';

class ExamView extends StatelessWidget {
  const ExamView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomExamViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(body: CustomExamViewBody());
    }
  }
}
