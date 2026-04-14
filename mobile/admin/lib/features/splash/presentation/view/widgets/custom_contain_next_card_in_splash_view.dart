// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/helpers/user_is_logged_in_helper.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomContainNextCardInSplashView extends StatelessWidget {
  const CustomContainNextCardInSplashView({super.key});

  @override
  Widget build(BuildContext context) {
    return TextButton(
      onPressed: () async {
        final isLoggedInUser = await userIsLoggedInHelper();
        pushGoRouterHelper(
          context: context,
          view: isLoggedInUser ? kHomeViewRouter : kAuthViewRouter,
        );
      },
      child: const FittedBox(
        child: TextMedium16Component(
          text: 'التالي',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.whiteColor,
        ),
      ),
    );
  }
}
