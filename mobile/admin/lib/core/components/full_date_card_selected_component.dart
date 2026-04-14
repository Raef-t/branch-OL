import 'package:flutter/material.dart';
import '/core/components/contain_full_date_card_selected_component.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/styles/colors_style.dart';

class FullDateCardSelectedComponent extends StatelessWidget {
  const FullDateCardSelectedComponent({
    super.key,
    required this.date,
    required this.day,
    required this.isSelectedCard,
    required this.onTap,
    required this.circleValues,
  });
  final String date, day;
  final bool isSelectedCard;
  final void Function() onTap;
  final int circleValues;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: isRotait
            ? OnlyPaddingWithoutChild.top8AndBottom4AndRight14AndLeft14(
                context: context,
              )
            : OnlyPaddingWithoutChild.top15AndBottom10AndRight10AndLeft10(
                context: context,
              ),
        decoration:
            BoxDecorations.boxDecorationToFullDateCardSelectedAndUnSelectedComponent(
              context: context,
              color: isSelectedCard
                  ? ColorsStyle.veryLittlePinkColor5
                  : ColorsStyle.backSection,
            ),
        child: ContainFullDateCardSelectedComponent(
          date: date,
          day: day,
          isSelectedCard: isSelectedCard,
          circleValues: circleValues,
        ),
      ),
    );
  }
}
