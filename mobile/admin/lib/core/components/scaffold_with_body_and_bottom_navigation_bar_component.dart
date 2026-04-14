import 'package:flutter/material.dart';
import '/core/components/q_r_bottom_navigation_bar_component.dart';
import '/core/styles/colors_style.dart';

class ScaffoldWithBodyAndBottomNavigationBarComponent extends StatelessWidget {
  const ScaffoldWithBodyAndBottomNavigationBarComponent({
    super.key,
    required this.body,
    this.bottomNavigationBar,
    this.currentIndex,
    this.onTap,
  });
  final Widget body;
  final Widget? bottomNavigationBar;
  final int? currentIndex;
  final void Function(int)? onTap;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: ColorsStyle.mediumWhiteColor4,
      body: body,
      bottomNavigationBar:
          bottomNavigationBar ??
          QRBottomNavigationBarComponent(
            currentIndex: currentIndex ?? 1,
            onTap: onTap ?? (currentIndex) {},
          ),
    );
  }
}
