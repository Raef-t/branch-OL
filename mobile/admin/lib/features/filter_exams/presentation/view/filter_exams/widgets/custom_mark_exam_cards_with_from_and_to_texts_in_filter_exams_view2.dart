import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_mark_exam_card_with_text_up_it_in_filter_exams_view2.dart';

class CustomMarkExamCardsWithFromAndToTextsInFilterExamsView2
    extends StatelessWidget {
  const CustomMarkExamCardsWithFromAndToTextsInFilterExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left38AndRight22(
      context: context,
      child: const Row(
        children: [
          CustomMarkExamCardWithTextUpItInFilterExamsView2(text: 'إلى'),
          Spacer(),
          CustomMarkExamCardWithTextUpItInFilterExamsView2(text: 'من'),
        ],
      ),
    );
  }
}
