import 'package:flutter/material.dart';
import '/core/components/see_more_text_and_another_text_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/styles/texts_style.dart';

class CustomSeeMoreTextWithAnotherTextToExamsInHomeView
    extends StatelessWidget {
  const CustomSeeMoreTextWithAnotherTextToExamsInHomeView({super.key});
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left18AndRight20(
      context: context,
      child: SeeMoreTextAndAnotherTextComponent(
        text: 'المذاكرات',
        onTap: () => pushGoRouterHelper(
          context: context,
          view: kExamViewToHoleAcademicRouter,
        ),
        textStyleToAnotherText: TextsStyle.medium16(context: context),
        textStyleToSeeMoreText: TextsStyle.semiBold10(context: context),
      ),
    );
  }
}
