import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_filter_exams_view_body2.dart';

class FilterExams2View extends StatelessWidget {
  const FilterExams2View({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomFilterExamsViewBody2(),
      );
    } else {
      return const ScaffoldWithBodyComponent(
        body: CustomFilterExamsViewBody2(),
      );
    }
  }
}
