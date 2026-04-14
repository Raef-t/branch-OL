import 'package:flutter/material.dart';
import '/core/components/contain_full_date_card_un_selected_component.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/styles/colors_style.dart';

class FullDateCardUnSelectedComponent extends StatelessWidget {
  const FullDateCardUnSelectedComponent({
    super.key,
    required this.date,
    required this.day,
  });
  final String date, day;
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: OnlyPaddingWithoutChild.top8AndBottom4AndRight14AndLeft14(
        context: context,
      ),
      decoration:
          BoxDecorations.boxDecorationToFullDateCardSelectedAndUnSelectedComponent(
            context: context,
            color: ColorsStyle.whiteColor,
          ),
      child: ContainFullDateCardUnSelectedComponent(date: date, day: day),
    );
  }
}
