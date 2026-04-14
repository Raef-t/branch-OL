import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_student_circle_in_student_view.dart';
import '/gen/fonts.gen.dart';

class CustomLeftSideTheCardInCoursesView extends StatelessWidget {
  const CustomLeftSideTheCardInCoursesView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel? academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Column(
      children: [
        isRotait ? const SizedBox.shrink() : Heights.height10(context: context),
        const CustomStudentCircleInStudentView(),
        Heights.height5(context: context),
        TextMedium16Component(
          text: academicBranchesModel?.studentsCount.toString() ?? '0',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumPinkColor,
        ),
      ],
    );
  }
}
