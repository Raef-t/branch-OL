import 'package:flutter/material.dart';
import '/features/courses/presentation/view/widgets/custom_sliver_app_bar_in_courses_view.dart';
import '/features/courses/presentation/view/widgets/custom_sliver_fill_remaining_in_courses_view.dart';

class CustomCoursesViewBody extends StatelessWidget {
  const CustomCoursesViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        const CustomSliverAppBarInCoursesView(),
        const CustomSliverFillRemainingInCoursesView(),
        SliverPadding(
          padding: EdgeInsets.only(
            bottom:
                MediaQuery.of(context).padding.bottom +
                kBottomNavigationBarHeight +
                14,
          ),
        ),
      ],
    );
  }
}
