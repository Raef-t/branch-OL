import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_contain_card_in_apply_tab_view_in_work_hours_view.dart';

class CustomCardInApplyTabViewInWorkHoursView extends StatelessWidget {
  const CustomCardInApplyTabViewInWorkHoursView({
    super.key,
    required this.percentHeight,
    required this.color,
  });
  final double percentHeight;
  final Color color;
  @override
  Widget build(BuildContext context) {
    double height = MediaQuery.sizeOf(context).height;
    return Container(
      height: height * percentHeight,
      padding: OnlyPaddingWithoutChild.left9AndRight10AndTop14AndBottom14(
        context: context,
      ),
      decoration:
          BoxDecorations.boxDecorationToCardInApplyTabViewInWorkHoursView(
            context: context,
            color: color,
          ),
      child: const CustomContainCardInApplyTabViewInWorkHoursView(),
    );
  }
}
