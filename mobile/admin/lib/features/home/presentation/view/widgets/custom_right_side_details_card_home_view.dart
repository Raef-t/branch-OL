import 'package:flutter/material.dart';
import '/features/home/presentation/view/widgets/custom_left_arrow_to_determined_thing_image_home_view.dart';
import '/features/home/presentation/view/widgets/custom_two_text_times_in_details_card_home_view.dart';

class CustomRightSideDetailsCardHomeView extends StatelessWidget {
  const CustomRightSideDetailsCardHomeView({
    super.key,
    required this.firstTime,
    required this.secondTime,
  });
  final String firstTime, secondTime;
  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        CustomTwoTextTimesInDetailsCardHomeView(
          firstTime: firstTime,
          secondTime: secondTime,
        ),

        const Spacer(),
        const CustomLeftArrowToDeterminedThingImageHomeView(),
      ],
    );
  }
}
