import 'package:flutter/material.dart';
import '/gen/assets.gen.dart';

class CustomLogoImageInAuthView extends StatelessWidget {
  const CustomLogoImageInAuthView({super.key});

  @override
  Widget build(BuildContext context) {
    double height = MediaQuery.sizeOf(context).height;
    double width = MediaQuery.sizeOf(context).width;
    return Assets.images.logoOlamaaImage.image(
      width: width * 0.36,
      height: height * 0.15,
      fit: BoxFit.fill,
    );
  }
}
