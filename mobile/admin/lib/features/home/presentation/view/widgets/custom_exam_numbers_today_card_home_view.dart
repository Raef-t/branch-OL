import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/home/presentation/view/widgets/custom_contain_exam_numbers_today_card_home_view.dart';

class CustomExamNumbersTodayCardHomeView extends StatelessWidget {
  const CustomExamNumbersTodayCardHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      margin: OnlyPaddingWithoutChild.left18AndRight20(context: context),
      padding: OnlyPaddingWithoutChild.top15AndBottom10AndRight18AndLeft18(
        context: context,
      ),
      decoration: BoxDecorations.boxDecorationToExamNumbersTodayCardHomeView(
        context: context,
      ),
      child: const CustomContainExamNumbersTodayCardHomeView(),
    );
  }
}
