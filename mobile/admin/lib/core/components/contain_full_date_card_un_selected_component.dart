import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';

class ContainFullDateCardUnSelectedComponent extends StatelessWidget {
  const ContainFullDateCardUnSelectedComponent({
    super.key,
    required this.date,
    required this.day,
  });
  final String date, day;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text(date, style: TextsStyle.semiBold16(context: context)),
        Text(
          day,
          style: TextsStyle.medium12(
            context: context,
          ).copyWith(color: ColorsStyle.greyColor),
        ),
      ],
    );
  }
}
