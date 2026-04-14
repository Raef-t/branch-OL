import 'package:flutter/material.dart';
import '/core/components/circle_date_inside_full_date_card_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/texts_style.dart';

class ContainFullDateCardSelectedComponent extends StatelessWidget {
  const ContainFullDateCardSelectedComponent({
    super.key,
    required this.date,
    required this.day,
    required this.isSelectedCard,
    required this.circleValues,
  });
  final String date, day;
  final bool isSelectedCard;
  final int circleValues;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Column(
      children: [
        Text(date, style: TextsStyle.semiBold16(context: context)),
        Text(day, style: TextsStyle.medium12(context: context)),
        isRotait
            ? Heights.height4(context: context)
            : Heights.height10(context: context),
        Visibility(
          visible: isSelectedCard,
          child: CircleDateInsideFullDateCardComponent(
            circleValues: circleValues,
          ),
        ),
      ],
    );
  }
}
