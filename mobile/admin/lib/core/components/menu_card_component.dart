import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/contain_menu_card_component.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/styles/colors_style.dart';

class MenuCardComponent extends StatelessWidget {
  const MenuCardComponent({
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
    return Container(
      padding: OnlyPaddingWithoutChild.left22AndTop14AndRight31AndBottom15(
        context: context,
      ),
      margin: OnlyPaddingWithoutChild.left37AndRight18AndBottom15(
        context: context,
      ),
      decoration: BoxDecorations.boxDecorationToCardsInMenuTabBarWorkHoursView(
        color: ColorsStyle.whiteColor,
        borderRadius: Circulars.circular10(context: context),
      ),
      child: ContainMenuCardComponent(
        subjectName: subjectName,
        course: course,
        classRoom: classRoom,
        type: type,
        startTime: startTime,
        endTime: endTime,
      ),
    );
  }
}
