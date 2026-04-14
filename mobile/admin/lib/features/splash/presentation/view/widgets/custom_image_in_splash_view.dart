import 'package:flutter/material.dart';
import '/gen/assets.gen.dart';

class CustomImageInSplashView extends StatelessWidget {
  const CustomImageInSplashView({super.key});

  @override
  Widget build(BuildContext context) {
    return Positioned.fill(
      child: Assets.images.studentGirlImage.image(fit: BoxFit.cover),
    );
  }
}
