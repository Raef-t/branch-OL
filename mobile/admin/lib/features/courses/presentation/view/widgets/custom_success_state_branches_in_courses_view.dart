import 'package:flutter/material.dart';
import '/core/lists/all_colors_to_card_in_courses_view_list.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses/presentation/view/widgets/custom_card_with_circle_in_courses_view.dart';

class CustomSuccessStateBranchesInCoursesView extends StatelessWidget {
  const CustomSuccessStateBranchesInCoursesView({
    super.key,
    required this.length,
    required this.listOfAcademicBranchesModel,
  });
  final int length;
  final List<AcademicBranchesToCoursesModel> listOfAcademicBranchesModel;
  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      clipBehavior: Clip.none,
      reverse: true,
      padding: OnlyPaddingWithoutChild.left15AndRight15(context: context),
      child: Row(
        textDirection: TextDirection.rtl,
        children: List.generate(length, (index) {
          final academicBranchesModel = listOfAcademicBranchesModel[index];
          final color =
              allColorsToCardInCoursesViewList[index %
                  allColorsToCardInCoursesViewList.length];
          return Padding(
            padding: EdgeInsets.only(
              left: MediaQuery.sizeOf(context).width * 0.011,
            ),
            child: CustomCardWithCircleInCoursesView(
              academicBranchesModel: academicBranchesModel,
              color: color,
            ),
          );
        }),
      ),
    );
  }
}
