import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomDateInSubtitleListTileInPaymentsView extends StatelessWidget {
  const CustomDateInSubtitleListTileInPaymentsView({
    super.key,
    required this.date,
  });
  final String date;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Assets.images.dateImage.image(),
        Widths.width8(context: context),
        TextMedium12Component(
          text: date,
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.greyColor,
        ),
      ],
    );
  }
}
