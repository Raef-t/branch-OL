import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_details_about_teacher_and_class_number_inside_card_in_courses_view.dart';

class CustomBottomSectionInRightSideTheCardInCoursesView
    extends StatelessWidget {
  const CustomBottomSectionInRightSideTheCardInCoursesView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right8(
      context: context,
      child: CustomDetailsAboutTeacherAndClassNumberInsideCardInCoursesView(
        academicBranchesModel: academicBranchesModel,
      ),
    );
  }
}
