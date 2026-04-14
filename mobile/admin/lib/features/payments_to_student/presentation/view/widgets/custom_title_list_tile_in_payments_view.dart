import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class CustomTitleListTileInPaymentsView extends StatelessWidget {
  const CustomTitleListTileInPaymentsView({super.key, required this.title});
  final String title;
  @override
  Widget build(BuildContext context) {
    return TextMedium14Component(
      text: title,
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
