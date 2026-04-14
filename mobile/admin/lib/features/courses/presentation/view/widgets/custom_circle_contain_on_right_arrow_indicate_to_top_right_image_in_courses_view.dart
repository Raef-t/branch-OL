// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/decorations/box_decorations.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';

class CustomCircleContainOnRightArrowIndicateToTopRightImageInCoursesView
    extends StatelessWidget {
  const CustomCircleContainOnRightArrowIndicateToTopRightImageInCoursesView({
    super.key,
    required this.academicBranchesModel,
  });
  final AcademicBranchesToCoursesModel academicBranchesModel;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return GestureDetector(
      onTap: () async {
        await StoreParametersInSharedPreferences.saveIntParameter(
          intValue: academicBranchesModel.id ?? 0,
          key: keyAcademicBranchIdInSharedPreferences,
        );
        pushGoRouterHelper(
          context: context,
          view: kCoursesDetailsViewRouter,
          extraObject: academicBranchesModel,
        );
      },
      child: Container(
        height: size.height * (isRotait ? 0.057 : 0.07),
        width: size.width * 0.096,
        decoration:
            BoxDecorations.boxDecorationToCircleThatContainOnRightArrowIndicateToTopRightImage(
              context: context,
            ),
      ),
    );
  }
}
