import 'package:flutter/material.dart';
import '/core/lists/intro_texts_in_splash_view_list.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/gen/fonts.gen.dart';

class CustomThreeTextsInSplashView extends StatelessWidget {
  const CustomThreeTextsInSplashView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: List.generate(introTextsInSplashViewList.length, (index) {
        final text = introTextsInSplashViewList[index];
        return Text(
          text,
          style: TextsStyle.medium18(context: context).copyWith(
            color: ColorsStyle.littleGreyColor,
            fontFamily: FontFamily.tajawal,
          ),
        );
      }),
    );
  }
}
