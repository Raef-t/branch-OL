import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_all_dates_cards_with_text_up_it_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_filter_text_with_image_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_mark_exam_cards_with_text_up_it_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_range_slider_with_theme_widget_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_save_button_card_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_subject_cards_with_text_up_it_in_filter_exams_view2.dart';

class CustomSliverFillRemainingInFilterExamsView2 extends StatelessWidget {
  const CustomSliverFillRemainingInFilterExamsView2({
    super.key,
    required this.rangeValues,
    required this.onChanged,
  });
  final RangeValues rangeValues;
  final void Function(RangeValues) onChanged;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height28(context: context),
            const CustomFilterTextWithImageInFilterExamsView2(),
            Heights.height29(context: context),
            const CustomAllDatesCardsWithTextUpItInFilterExamsView2(),
            Heights.height54(context: context),
            const CustomSubjectCardsWithTextUpItInFilterExamsView2(),
            Heights.height29(context: context),
            const CustomMarkExamCardsWithTextUpItInFilterExamsView2(),
            isRotait
                ? Heights.height40(context: context)
                : Heights.height58(context: context),
            CustomRangeSliderWithThemeWidgetInFilterExamsView2(
              rangeValues: rangeValues,
              onChanged: onChanged,
            ),
            Heights.height5(context: context),
            const CustomSaveButtonCardInFilterExamsView2(),
            Heights.height15(context: context),
          ],
        ),
      ),
    );
  }
}
