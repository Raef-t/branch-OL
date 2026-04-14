import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

class LittleBlackViewComponent extends StatelessWidget {
  const LittleBlackViewComponent({super.key});

  @override
  Widget build(BuildContext context) {
    return Positioned.fill(
      child: Container(color: ColorsStyle.blackColor.withAlpha(125)),
    );
  }
}
