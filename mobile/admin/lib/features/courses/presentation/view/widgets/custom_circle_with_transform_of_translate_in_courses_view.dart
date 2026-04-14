import 'package:flutter/material.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_circle_contain_on_right_arrow_indicate_to_top_right_image_in_courses_view.dart';

class CustomCircleWithTransformOfTranslateInCoursesView
    extends StatelessWidget {
  const CustomCircleWithTransformOfTranslateInCoursesView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    return CustomCircleContainOnRightArrowIndicateToTopRightImageInCoursesView(
      academicBranchesModel: academicBranchesModel,
    );
  }
}
