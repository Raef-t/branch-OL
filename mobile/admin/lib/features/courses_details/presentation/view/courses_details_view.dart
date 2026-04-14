import 'dart:io';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_courses_details_view_body.dart';

class CoursesDetailsView extends StatelessWidget {
  const CoursesDetailsView({super.key});
  @override
  Widget build(BuildContext context) {
    final academicBranchesModel =
        GoRouterState.of(context).extra as AcademicBranchesToCoursesModel;
    if (Platform.isIOS) {
      return CupertinoPageScaffoldWithChildComponent(
        child: CustomCoursesDetailsViewBody(
          academicBranchesModel: academicBranchesModel,
        ),
      );
    } else {
      return ScaffoldWithBodyComponent(
        body: CustomCoursesDetailsViewBody(
          academicBranchesModel: academicBranchesModel,
        ),
      );
    }
  }
}
