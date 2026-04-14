import 'package:flutter/material.dart';
import '/core/components/put_bottom_navigation_bar_on_view_in_ios_component.dart';

class ChildAndBottomNavigationBarInIosComponent extends StatelessWidget {
  const ChildAndBottomNavigationBarInIosComponent({
    super.key,
    required this.widget,
    this.bottomNavigationBar,
    this.currentIndex,
    this.onTap,
  });
  final Widget widget;
  final Widget? bottomNavigationBar;
  final int? currentIndex;
  final void Function(int)? onTap;
  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        widget,
        PutBottomNavigationBarOnViewInIosComponent(
          bottomNavigationBar: bottomNavigationBar,
          currentIndex: currentIndex,
          onTap: onTap,
        ),
      ],
    );
  }
}
