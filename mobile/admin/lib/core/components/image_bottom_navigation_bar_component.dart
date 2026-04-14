import 'package:flutter/material.dart';
import '/gen/assets.gen.dart';

class ImageBottomNavigationBarComponent extends StatelessWidget {
  const ImageBottomNavigationBarComponent({super.key});

  @override
  Widget build(BuildContext context) {
    double height = MediaQuery.sizeOf(context).height;
    final isRotait = MediaQuery.orientationOf(context) == Orientation.landscape;
    return SizedBox(
      width: double.infinity,
      child: Assets.images.bottomNavigationBarImage.image(
        fit: BoxFit.fill,
        height: height * (isRotait ? 0.17 : 0.115),
      ),
    );
  }
}
