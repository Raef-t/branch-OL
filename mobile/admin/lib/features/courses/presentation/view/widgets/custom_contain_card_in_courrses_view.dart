import 'package:flutter/material.dart';
import '/core/sized_boxs/widths.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_left_side_the_card_in_courses_view.dart';
import '/features/courses/presentation/view/widgets/custom_right_side_the_card_in_courses_view.dart';

class CustomContainCardInCourrsesView extends StatelessWidget {
  const CustomContainCardInCourrsesView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        CustomLeftSideTheCardInCoursesView(
          academicBranchesModel: academicBranchesModel,
        ),
        Widths.width27(context: context),
        Expanded(
          child: CustomRightSideTheCardInCoursesView(
            academicBranchesModel: academicBranchesModel,
          ),
        ),
      ],
    );
  }
}
