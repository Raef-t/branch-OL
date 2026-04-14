// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/decorations/box_decorations.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_contain_details_card_in_courses_details_view.dart';

class CustomDetailsCardInCoursesDetailsView extends StatelessWidget {
  const CustomDetailsCardInCoursesDetailsView({
    super.key,
    required this.circleColor,
    required this.verticalLineColor,
    required this.batchesModel,
  });
  final Color circleColor, verticalLineColor;
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () async {
        await StoreParametersInSharedPreferences.saveIntParameter(
          intValue: batchesModel.id ?? 0,
          key: keyBatchIdInSharedPreferences,
        );
        pushGoRouterHelper(
          context: context,
          view: kClassViewRouter,
          extraObject: batchesModel,
        );
      },
      child: Container(
        margin: OnlyPaddingWithoutChild.left40AndRight39AndBottom15(
          context: context,
        ),

        // padding: const EdgeInsets.only(left: 15, top: 16),
        decoration:
            BoxDecorations.boxDecorationToDetailsCardInCoursesDetailsView(
              context: context,
            ),
        child: CustomContainDetailsCardInCoursesDetailsView(
          circleColor: circleColor,
          verticalLineColor: verticalLineColor,
          batchesModel: batchesModel,
        ),
      ),
    );
  }
}
