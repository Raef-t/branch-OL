import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class CustomTrailingListTileInPaymentsView extends StatelessWidget {
  const CustomTrailingListTileInPaymentsView({super.key, required this.amount});
  final String amount;
  @override
  Widget build(BuildContext context) {
    return TextMedium14Component(
      text:
          r'$'
          '$amount',
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
