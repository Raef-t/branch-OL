import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

class DividerWithoutFixedSizeComponent extends StatelessWidget {
  const DividerWithoutFixedSizeComponent({super.key});

  @override
  Widget build(BuildContext context) {
    return const Divider(color: ColorsStyle.veryLittleWhiteColor2);
  }
}
