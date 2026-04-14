import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/home/presentation/view/widgets/custom_generate_details_card_home_view.dart';
import '/features/home/presentation/view/widgets/custom_see_more_text_with_another_text_to_work_hours_in_home_view.dart';

class CustomWorkHoursHomeViewSection extends StatelessWidget {
  const CustomWorkHoursHomeViewSection({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        const CustomSeeMoreTextWithAnotherTextToWorkHoursInHomeView(),
        Heights.height36(context: context),
        const CustomGenerateDetailsCardHomeView(),
      ],
    );
  }
}
