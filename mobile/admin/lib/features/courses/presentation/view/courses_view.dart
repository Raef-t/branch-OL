import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/child_and_bottom_navigation_bar_in_ios_component.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_and_bottom_navigation_bar_component.dart';
import '/features/courses/presentation/view/widgets/custom_courses_view_body.dart';

class CoursesView extends StatelessWidget {
  const CoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: ChildAndBottomNavigationBarInIosComponent(
          widget: CustomCoursesViewBody(),
        ),
      );
    } else {
      return const ScaffoldWithBodyAndBottomNavigationBarComponent(
        body: CustomCoursesViewBody(),
      );
    }
  }
}
