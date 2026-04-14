import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/home/presentation/view/widgets/custom_exam_numbers_today_card_home_view.dart';
import '/features/home/presentation/view/widgets/custom_see_more_text_with_another_text_to_exams_in_home_view.dart';

class CustomExamsAndNumberExamsTodayHomeViewSection extends StatelessWidget {
  const CustomExamsAndNumberExamsTodayHomeViewSection({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        const CustomSeeMoreTextWithAnotherTextToExamsInHomeView(),
        Heights.height17(context: context),
        const CustomExamNumbersTodayCardHomeView(),
      ],
    );
  }
}
