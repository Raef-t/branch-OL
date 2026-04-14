import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/components/text_medium13_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomContainRestAndTotalPriceCardInPaymentsView extends StatelessWidget {
  const CustomContainRestAndTotalPriceCardInPaymentsView({
    super.key,
    required this.firstText,
    required this.secondText,
  });
  final String firstText, secondText;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        FittedBox(
          child: TextMedium13Component(
            text: firstText,
            color: ColorsStyle.mediumBlackColor2,
          ),
        ),
        Heights.height25(context: context),
        FittedBox(
          child: TextMedium12Component(
            text:
                r'$'
                '$secondText',
            fontFamily: FontFamily.inter,
            color: ColorsStyle.greyColor,
          ),
        ),
      ],
    );
  }
}
