import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';
import '/core/styles/colors_style.dart';

class CustomTwoTextTimesInDetailsCardHomeView extends StatelessWidget {
  const CustomTwoTextTimesInDetailsCardHomeView({
    super.key,
    required this.firstTime,
    required this.secondTime,
  });
  final String firstTime, secondTime;
  @override
  Widget build(BuildContext context) {
    return SymmetricPaddingWithChild.vertical5(
      context: context,
      child: Column(
        children: [
          FittedBox(child: TextMedium14Component(text: firstTime)),
          const Spacer(),
          FittedBox(
            child: TextMedium14Component(
              text: secondTime,
              color: ColorsStyle.greyColor,
            ),
          ),
        ],
      ),
    );
  }
}
