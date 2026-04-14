import 'package:flutter/material.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_sliver_app_bar_in_courses_details_view.dart';
import '/features/courses_details/presentation/view/widgets/custom_sliver_fill_remaining_in_courses_details_view.dart';

class CustomCoursesDetailsViewBody extends StatefulWidget {
  const CustomCoursesDetailsViewBody({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  @override
  State<CustomCoursesDetailsViewBody> createState() =>
      _CustomCoursesDetailsViewBodyState();
}

class _CustomCoursesDetailsViewBodyState
    extends State<CustomCoursesDetailsViewBody> {
  int selectedIndex = 2;
  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        CustomSliverAppBarInCoursesDetailsView(
          appBarCourseName:
              widget.academicBranchesModel.courseName ?? 'لا يوجد دورة',
        ),
        CustomSliverFillRemainingInCoursesDetailsView(
          selectedIndex: selectedIndex,
          onTapSelected: (index) => setState(() => selectedIndex = index),
          academicBranchesModel: widget.academicBranchesModel,
        ),
      ],
    );
  }
}
