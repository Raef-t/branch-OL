import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/details_students/presentation/view/widgets/custom_details_student_view_body.dart';

class DetailsStudentView extends StatelessWidget {
  const DetailsStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomDetailsStudentViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(
        body: CustomDetailsStudentViewBody(),
      );
    }
  }
}
