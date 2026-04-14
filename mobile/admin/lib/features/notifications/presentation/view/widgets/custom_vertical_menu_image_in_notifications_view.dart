import 'package:flutter/material.dart';
import '/gen/assets.gen.dart';

class CustomVerticalMenuImageInNotificationsView extends StatelessWidget {
  const CustomVerticalMenuImageInNotificationsView({
    super.key,
    required this.onTap,
  });
  final void Function() onTap;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return GestureDetector(
      onTap: onTap,
      child: SizedBox(
        height: size.height * 0.035,
        width: size.width * 0.06,
        child: Assets.images.verticalMenuImage.image(),
      ),
    );
  }
}
