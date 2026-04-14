import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_contain_card_in_courrses_view.dart';

class CustomCardInCoursesView extends StatelessWidget {
  const CustomCardInCoursesView({
    super.key,
    required this.academicBranchesModel,
    required this.color,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  final Color color;
  @override
  Widget build(BuildContext context) {
    return Container(
      width: MediaQuery.sizeOf(context).width * 0.64,
      padding: OnlyPaddingWithoutChild.left14AndTop10AndRight8AndBottom13(
        context: context,
      ),
      decoration: BoxDecorations.boxDecorationToCardInCoursesView(
        context: context,
        color: color,
      ),
      child: CustomContainCardInCourrsesView(
        academicBranchesModel: academicBranchesModel,
      ),
    );
  }
}
