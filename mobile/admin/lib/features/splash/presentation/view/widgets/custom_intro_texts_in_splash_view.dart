import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/texts_style.dart';
import '/features/splash/presentation/view/widgets/custom_three_texts_in_splash_view.dart';

class CustomIntroTextsInSplashView extends StatelessWidget {
  const CustomIntroTextsInSplashView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Text(
          'العلماء للتعليم طريقك للنجاح',
          style: TextsStyle.medium24(context: context),
        ),
        Heights.height16(context: context),
        const CustomThreeTextsInSplashView(),
      ],
    );
  }
}
