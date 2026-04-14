import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_generate_current_week_cards_in_exams_to_student_view.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_generate_today_cards_in_exams_to_student_view.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_sliver_app_bar_in_exams_to_student_view.dart';
import '/features/exams_to_student/presentation/view/widgets/custom_text_with_align_and_padding_in_exams_to_student_view.dart';

class CustomExamsToStudentViewBody extends StatelessWidget {
  const CustomExamsToStudentViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        const CustomSliverAppBarInExamsToStudentView(),
        SliverFillRemaining(
          hasScrollBody: false,
          child: BackgroundBodyToViewsComponent(
            child: Column(
              children: [
                Heights.height39(context: context),
                const CustomTextWithAlignAndPaddingInExamsToStudentView(
                  text: 'اليوم',
                ),
                Heights.height18(context: context),
                const CustomGenerateTodayCardsInExamsToStudentView(),
                Heights.height32(context: context),
                const CustomTextWithAlignAndPaddingInExamsToStudentView(
                  text: 'الاسبوع الحالي',
                ),
                Heights.height18(context: context),
                const CustomGenerateCurrentWeekCardsInExamsToStudentView(),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
