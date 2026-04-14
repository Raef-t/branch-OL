import 'package:flutter/material.dart';
import '/core/classes/bottom_left_subtract_clipper_for_horizontal_class.dart';
import '/core/classes/bottom_left_subtract_clipper_for_vertical_class.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_card_in_courses_view.dart';

class CustomCardWithClipPathInCoursesView extends StatelessWidget {
  const CustomCardWithClipPathInCoursesView({
    super.key,
    required this.academicBranchesModel,
    required this.color,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  final Color color;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    final extraHeight = MediaQuery.sizeOf(context).height * 0.03;
    return ClipPath(
      clipper: isRotait
          ? BottomLeftSubtractClipperForVerticalClass(extraHeight: extraHeight)
          : BottomLeftSubtractClipperForHorizontalClass(
              extraHeight: extraHeight,
            ),
      child: CustomCardInCoursesView(
        academicBranchesModel: academicBranchesModel,
        color: color,
      ),
    );
  }
}
