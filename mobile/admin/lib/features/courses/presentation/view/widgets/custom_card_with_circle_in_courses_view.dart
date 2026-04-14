import 'package:flutter/material.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_card_with_clip_path_in_courses_view.dart';
import '/features/courses/presentation/view/widgets/custom_circle_with_transform_of_translate_in_courses_view.dart';

class CustomCardWithCircleInCoursesView extends StatelessWidget {
  const CustomCardWithCircleInCoursesView({
    super.key,
    required this.academicBranchesModel,
    required this.color,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  final Color color;
  @override
  Widget build(BuildContext context) {
    return Container(
      margin: EdgeInsets.only(left: MediaQuery.sizeOf(context).width * 0.0365),
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          CustomCardWithClipPathInCoursesView(
            academicBranchesModel: academicBranchesModel,
            color: color,
          ),
          Positioned(
            bottom: -1.5,
            child: CustomCircleWithTransformOfTranslateInCoursesView(
              academicBranchesModel: academicBranchesModel,
            ),
          ),
        ],
      ),
    );
  }
}
