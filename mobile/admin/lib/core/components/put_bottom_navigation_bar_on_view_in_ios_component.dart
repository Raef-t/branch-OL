import 'package:flutter/material.dart';
import '/core/components/q_r_bottom_navigation_bar_component.dart';

class PutBottomNavigationBarOnViewInIosComponent extends StatelessWidget {
  const PutBottomNavigationBarOnViewInIosComponent({
    super.key,
    this.bottomNavigationBar,
    this.currentIndex,
    this.onTap,
  });

  final Widget? bottomNavigationBar;
  final int? currentIndex;
  final void Function(int)? onTap;

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.bottomCenter,
      child:
          bottomNavigationBar ??
          QRBottomNavigationBarComponent(
            currentIndex: currentIndex!,
            onTap: onTap!,
          ),
    );
  }
}
