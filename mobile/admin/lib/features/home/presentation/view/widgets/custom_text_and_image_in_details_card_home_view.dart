import 'package:flutter/material.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/texts_style.dart';
import '/gen/fonts.gen.dart';

class CustomTextAndImageInDetailsCardHomeView extends StatelessWidget {
  const CustomTextAndImageInDetailsCardHomeView({
    super.key,
    required this.text,
    required this.pathImage,
  });
  final String text, pathImage;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.landscape;
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        Text(
          text,
          style: TextsStyle.normal10(
            context: context,
          ).copyWith(fontFamily: FontFamily.tajawal),
        ),
        Widths.width9(context: context),
        Image.asset(
          pathImage,
          height: size.height * (isRotait ? 0.021 : 0.012),
          width: size.width * (isRotait ? 0.037 : 0.021),
        ),
      ],
    );
  }
}
