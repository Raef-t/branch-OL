import 'package:flutter/material.dart';

class VerticalDividerComponent extends StatelessWidget {
  const VerticalDividerComponent({
    super.key,
    required this.color,
    required this.thickness,
    required this.width,
  });
  final Color color;
  final double thickness, width;
  @override
  Widget build(BuildContext context) {
    return VerticalDivider(color: color, thickness: thickness, width: width);
  }
}
