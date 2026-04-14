import 'package:flutter/material.dart';
import '/features/class/presentation/view/widgets/custom_take_presence_card_in_bottom_navigation_bar_in_class_view.dart';

class CustomTakePresenceWidgetInBottomNavigationBarInClassView
    extends StatelessWidget {
  const CustomTakePresenceWidgetInBottomNavigationBarInClassView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return Positioned(
      bottom: size.height * 0.13,
      right: size.width * 0.256,
      child: const Center(
        child: CustomTakePresenceCardInBottomNavigationBarInClassView(),
      ),
    );
  }
}
