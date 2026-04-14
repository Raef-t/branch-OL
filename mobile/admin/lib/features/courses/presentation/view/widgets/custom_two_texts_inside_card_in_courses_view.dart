import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';

class CustomTwoTextsInsideCardInCoursesView extends StatelessWidget {
  const CustomTwoTextsInsideCardInCoursesView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel? academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        const TextMedium14Component(
          text: 'راما الأحمد',
          color: ColorsStyle.mediumBrownColor,
        ),
        Heights.height14(context: context),
        TextMedium14Component(
          text:
              academicBranchesModel?.batchesCount.toString() ??
              'لا يوجد عدد دورات',
          color: ColorsStyle.greyColor,
        ),
      ],
    );
  }
}
