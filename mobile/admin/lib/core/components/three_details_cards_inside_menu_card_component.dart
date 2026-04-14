import 'package:flutter/material.dart';
import '/core/components/details_card_inside_menu_card_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';

class ThreeDetailsCardsInsideMenuCardComponent extends StatelessWidget {
  const ThreeDetailsCardsInsideMenuCardComponent({
    super.key,
    required this.course,
    required this.classRoom,
    required this.type,
  });
  final String course, classRoom, type;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        Flexible(
          child: DetailsCardInsideMenuCardComponent(
            colorToCard: ColorsStyle.veryLittleOrangeColor,
            text: course,
            colorToText: ColorsStyle.mediumOrangeColor,
          ),
        ),
        Widths.width10(context: context),
        DetailsCardInsideMenuCardComponent(
          colorToCard: ColorsStyle.veryLittlePinkColor,
          text: classRoom,
          colorToText: ColorsStyle.pinkColor,
        ),
        Widths.width10(context: context),
        DetailsCardInsideMenuCardComponent(
          colorToCard: ColorsStyle.veryLittleGreenColor,
          text: type,
          colorToText: ColorsStyle.greenColor,
        ),
      ],
    );
  }
}
