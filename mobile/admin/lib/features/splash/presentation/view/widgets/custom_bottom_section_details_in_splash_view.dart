import 'package:flutter/material.dart';
import '/core/components/backdrop_filter_class_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/splash/presentation/view/widgets/custom_intro_texts_in_splash_view.dart';
import '/features/splash/presentation/view/widgets/custom_next_card_in_splash_view.dart';

class CustomBottomSectionDetailsInSplashView extends StatelessWidget {
  const CustomBottomSectionDetailsInSplashView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return Positioned(
      bottom: size.height * 0.07,
      right: size.width * 0.058,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          const CustomIntroTextsInSplashView(),
          Heights.height52(context: context),
          const BackdropFilterClassComponent(
            child: CustomNextCardInSplashView(),
          ),
        ],
      ),
    );
  }
}
