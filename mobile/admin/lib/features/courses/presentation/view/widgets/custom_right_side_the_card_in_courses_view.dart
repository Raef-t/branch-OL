import 'package:flutter/material.dart';
import '/core/components/text_medium18_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_bottom_section_in_right_side_the_card_in_courses_view.dart';
import '/gen/fonts.gen.dart';

class CustomRightSideTheCardInCoursesView extends StatelessWidget {
  const CustomRightSideTheCardInCoursesView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        isRotait ? const SizedBox.shrink() : Heights.height10(context: context),
        Heights.height3(context: context),
        TextMedium18Component(
          text: academicBranchesModel.courseName ?? 'لا يوجد دورة',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBlackColor2,
          overflow: TextOverflow.ellipsis,
          maxLines: 1,
        ),
        Heights.height12(context: context),
        CustomBottomSectionInRightSideTheCardInCoursesView(
          academicBranchesModel: academicBranchesModel,
        ),
        Heights.height37(context: context),
        SizedBox(height: MediaQuery.sizeOf(context).height * 0.03),
      ],
    );
  }
}
