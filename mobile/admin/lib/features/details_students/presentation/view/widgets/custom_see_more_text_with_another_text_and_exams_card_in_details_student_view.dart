import 'package:flutter/material.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/heights.dart';
import '/features/details_students/presentation/view/widgets/custom_generate_exam_cards_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_see_more_text_with_another_text_to_payments_in_details_student_view.dart';

class CustomSeeMoreTextWithAnotherTextAndExamsCardInDetailsStudentView
    extends StatelessWidget {
  const CustomSeeMoreTextWithAnotherTextAndExamsCardInDetailsStudentView({
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left25AndRight15(
      context: context,
      child: Column(
        children: [
          CustomSeeMoreTextWithAnotherTextInDetailsStudentView(
            text: 'العلامات',
            onTap: () =>
                pushGoRouterHelper(context: context, view: kExamsView2Router),
          ),
          Heights.height16(context: context),
          const CustomGenerateExamCardsInDetailsStudentView(),
        ],
      ),
    );
  }
}
