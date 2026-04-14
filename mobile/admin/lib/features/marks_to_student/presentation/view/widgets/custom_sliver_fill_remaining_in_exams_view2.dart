import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_generate_current_week_exam_cards_in_exams_view2.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_generate_last_week_exam_cards_in_exams_view2.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_header_in_exams_view2.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_text_with_padding_in_exams_view2.dart';

class CustomSliverFillRemainingInExamsView2 extends StatelessWidget {
  const CustomSliverFillRemainingInExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height34(context: context),
            const CustomHeaderInExamsView2(),
            Heights.height40(context: context),
            const CustomTextWithPaddingInExamsView2(text: 'الاسبوع الحالي'),
            Heights.height24(context: context),
            const CustomGenerateCurrentWeekExamCardsInExamsView2(),
            Heights.height20(context: context),
            const CustomTextWithPaddingInExamsView2(text: 'الاسبوع الماضي'),
            Heights.height24(context: context),
            const CustomGenerateLastWeekExamCardsInExamsView2(),
          ],
        ),
      ),
    );
  }
}
