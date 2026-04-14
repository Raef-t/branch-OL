import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class VerticalLineThatClipperFromTopLeftAndBottomLeftComponent
    extends StatelessWidget {
  const VerticalLineThatClipperFromTopLeftAndBottomLeftComponent({
    super.key,
    required this.color,
  });
  final Color color;
  @override
  Widget build(BuildContext context) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return Container(
      height: height * 0.165,
      width: width * 0.015,
      // margin: const EdgeInsets.only(bottom: 16),
      decoration:
          BoxDecorations.boxDecorationVerticalLineThatClipperFromTopLeftAndBottomLeftComponent(
            context: context,
            color: color,
          ),
    );
  }
}
