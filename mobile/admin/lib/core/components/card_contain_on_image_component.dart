import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class CardContainOnImageComponent extends StatelessWidget {
  const CardContainOnImageComponent({super.key, required this.imageProvider});
  final ImageProvider imageProvider;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Container(
      height: size.height * (isRotait ? 0.047 : 0.075),
      width: size.width * (isRotait ? 0.077 : 0.06),
      decoration: BoxDecorations.boxDecorationToCardContainOnImageComponent(
        context: context,
        imageProvider: imageProvider,
      ),
    );
  }
}
