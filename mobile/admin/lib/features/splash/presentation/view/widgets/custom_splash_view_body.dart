import 'package:flutter/material.dart';
import '/features/splash/presentation/view/widgets/custom_black_background_in_splash_view.dart';
import '/features/splash/presentation/view/widgets/custom_bottom_section_details_in_splash_view.dart';
import '/features/splash/presentation/view/widgets/custom_image_in_splash_view.dart';

class CustomSplashViewBody extends StatelessWidget {
  const CustomSplashViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return const Stack(
      children: [
        CustomImageInSplashView(),
        CustomBlackBackgroundInSplashView(),
        CustomBottomSectionDetailsInSplashView(),
      ],
    );
  }
}
