import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/symmetric_padding_without_child.dart';
import '/features/class/presentation/view/widgets/custom_contain_take_presence_card_in_bottom_navigation_bar_in_class_view.dart';

class CustomTakePresenceCardInBottomNavigationBarInClassView
    extends StatelessWidget {
  const CustomTakePresenceCardInBottomNavigationBarInClassView({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: SymmetricPaddingWithoutChild.horizontal15(context: context),
      decoration:
          BoxDecorations.boxDecorationToTakePresenceCardInBottomNavigationBarInClassView(
            context: context,
          ),
      child:
          const CustomContainTakePresenceCardInBottomNavigationBarInClassView(),
    );
  }
}
