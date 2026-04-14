import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/features/splash/presentation/view/widgets/custom_contain_next_card_in_splash_view.dart';

class CustomNextCardInSplashView extends StatelessWidget {
  const CustomNextCardInSplashView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return Container(
      height: size.height * 0.067,
      width: size.width * 0.342,
      decoration: BoxDecorations.boxDecorationToNextCardInSplashView(
        context: context,
      ),
      child: const CustomContainNextCardInSplashView(),
    );
  }
}
