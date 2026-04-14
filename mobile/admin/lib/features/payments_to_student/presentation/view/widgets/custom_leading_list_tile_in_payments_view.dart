import 'package:flutter/material.dart';
import '/core/components/medium_circle_dot_component.dart';

class CustomLeadingListTileInPaymentsView extends StatelessWidget {
  const CustomLeadingListTileInPaymentsView({super.key, required this.color});
  final Color color;
  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.topRight,
      widthFactor: 1, //take fixed width to make the Align work
      child: MediumCircleDotComponent(color: color),
    );
  }
}
