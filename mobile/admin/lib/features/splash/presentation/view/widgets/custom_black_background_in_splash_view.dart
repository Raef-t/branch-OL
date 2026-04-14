import 'package:flutter/cupertino.dart';
import '/core/decorations/box_decorations.dart';

class CustomBlackBackgroundInSplashView extends StatelessWidget {
  const CustomBlackBackgroundInSplashView({super.key});

  @override
  Widget build(BuildContext context) {
    double height = MediaQuery.sizeOf(context).height;
    return Positioned(
      bottom: 0,
      left: 0,
      right: 0,
      child: Container(
        height: height * 0.45,
        decoration: BoxDecorations.boxDecorationToBlackBackgroundInSplashView(),
      ),
    );
  }
}
