import 'package:flutter/material.dart';
import '/core/components/left_side_inside_menu_card_component.dart';
import '/core/components/right_side_inside_menu_card_component.dart';
import '/core/components/vertical_divider_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';

class ContainMenuCardComponent extends StatelessWidget {
  const ContainMenuCardComponent({
    super.key,
    required this.subjectName,
    required this.course,
    required this.classRoom,
    required this.type,
    required this.startTime,
    required this.endTime,
  });
  final String subjectName, course, classRoom, type, startTime, endTime;
  @override
  Widget build(BuildContext context) {
    return IntrinsicHeight(
      child: Row(
        children: [
          Expanded(
            child: LeftSideInsideMenuCardComponent(
              subjectName: subjectName,
              course: course,
              classRoom: classRoom,
              type: type,
            ),
          ),
          Widths.width8(context: context),
          const VerticalDividerComponent(
            color: ColorsStyle.veryLittleGreyColor,
            thickness: 0,
            width: 0,
          ),
          Widths.width6(context: context),
          RightSideInsideMenuCardComponent(
            startTime: startTime,
            endTime: endTime,
          ),
        ],
      ),
    );
  }
}
