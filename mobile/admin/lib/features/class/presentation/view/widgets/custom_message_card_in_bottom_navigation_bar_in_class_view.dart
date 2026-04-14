import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/symmetric_padding_without_child.dart';
import '/features/class/presentation/view/widgets/custom_contain_message_card_in_bottom_navigation_bar_in_class_view.dart';

class CustomMessageCardInBottomNavigationBarInClassView
    extends StatelessWidget {
  const CustomMessageCardInBottomNavigationBarInClassView({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: SymmetricPaddingWithoutChild.horizontal15(context: context),
      decoration:
          BoxDecorations.boxDecorationToMessageCardInBottomNavigationBarInClassView(
            context: context,
          ),
      child: const CustomContainMessageCardInBottomNavigationBarInClassView(),
    );
  }
}
