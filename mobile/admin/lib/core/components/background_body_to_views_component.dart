import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class BackgroundBodyToViewsComponent extends StatelessWidget {
  const BackgroundBodyToViewsComponent({super.key, required this.child});
  final Widget child;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecorations.boxDecorationToBackgroundBodyView(
        context: context,
      ),
      child: child,
    );
  }
}
