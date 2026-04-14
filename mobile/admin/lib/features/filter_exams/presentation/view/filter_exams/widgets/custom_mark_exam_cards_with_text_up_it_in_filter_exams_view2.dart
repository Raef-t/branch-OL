import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_mark_exam_cards_with_from_and_to_texts_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_text_with_padding_in_filter_exams_view2.dart';

class CustomMarkExamCardsWithTextUpItInFilterExamsView2
    extends StatelessWidget {
  const CustomMarkExamCardsWithTextUpItInFilterExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        const CustomTextWithPaddingInFilterExamsView2(text: 'العلامة'),
        Heights.height15(context: context),
        const CustomMarkExamCardsWithFromAndToTextsInFilterExamsView2(),
      ],
    );
  }
}
