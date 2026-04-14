import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_generate_subject_cards_in_filter_exams_view2.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_text_with_padding_in_filter_exams_view2.dart';

class CustomSubjectCardsWithTextUpItInFilterExamsView2 extends StatelessWidget {
  const CustomSubjectCardsWithTextUpItInFilterExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        const CustomTextWithPaddingInFilterExamsView2(text: 'المواد'),
        Heights.height15(context: context),
        const CustomGenerateSubjectCardsInFilterExamsView2(),
      ],
    );
  }
}
