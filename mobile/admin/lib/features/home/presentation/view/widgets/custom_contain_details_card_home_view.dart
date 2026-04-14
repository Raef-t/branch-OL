import 'package:flutter/material.dart';
import '/core/sized_boxs/widths.dart';
import '/features/home/presentation/view/widgets/custom_left_side_in_details_card_home_view.dart';
import '/features/home/presentation/view/widgets/custom_right_side_details_card_home_view.dart';

class CustomContainDetailsCardHomeView extends StatelessWidget {
  const CustomContainDetailsCardHomeView({
    super.key,
    required this.firstTime,
    required this.secondTime,
    required this.color,
    required this.subjectName,
    required this.onLeftArrowTap,
    required this.onRightArrowTap,
    required this.selectedCardInSameTime,
    required this.length,
    required this.course,
    required this.classRoom,
    required this.supervioserName,
    required this.imageUrl,
  });
  final String firstTime,
      secondTime,
      subjectName,
      course,
      classRoom,
      supervioserName,
      imageUrl;
  final Color color;
  final void Function() onLeftArrowTap, onRightArrowTap;
  final int selectedCardInSameTime, length;
  @override
  Widget build(BuildContext context) {
    return IntrinsicHeight(
      child: Row(
        children: [
          Expanded(
            flex: 3,
            child: CustomLeftSideInDetailsCardHomeView(
              color: color,
              subjectTime: subjectName,
              onLeftArrowTap: onLeftArrowTap,
              onRightArrowTap: onRightArrowTap,
              selectedCardInSameTime: selectedCardInSameTime,
              length: length,
              classRoom: classRoom,
              imageUrl: imageUrl,
              supervioserName: supervioserName,
              course: course,
            ),
          ),
          Widths.width10(context: context),
          Expanded(
            child: CustomRightSideDetailsCardHomeView(
              firstTime: firstTime,
              secondTime: secondTime,
            ),
          ),
        ],
      ),
    );
  }
}
