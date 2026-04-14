import 'package:flutter/material.dart';
import '/features/class/presentation/view/widgets/custom_message_card_in_bottom_navigation_bar_in_class_view.dart';

class CustomMessageWidgetInBottomNavigationBarInClassView
    extends StatelessWidget {
  const CustomMessageWidgetInBottomNavigationBarInClassView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return Positioned(
      bottom: size.height * 0.10,
      left: size.width * 0.256,
      child: const Center(
        child: CustomMessageCardInBottomNavigationBarInClassView(),
      ),
    );
  }
}
