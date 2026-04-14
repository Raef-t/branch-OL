import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_world_image_in_courses_view.dart';

class CustomDetailsAboutTeacherAndClassNumberInsideCardInCoursesView
    extends StatelessWidget {
  const CustomDetailsAboutTeacherAndClassNumberInsideCardInCoursesView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel? academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        TextMedium14Component(
          text:
              academicBranchesModel?.batchesCount.toString() ??
              'لا يوجد عدد دورات',
          color: ColorsStyle.greyColor,
        ),
        Widths.width6(context: context),
        const CustomWorldImageInCoursesView(),
      ],
    );
  }
}
