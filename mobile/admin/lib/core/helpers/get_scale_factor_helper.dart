import 'package:flutter/material.dart';

double getScaleFactorHelper({required BuildContext context}) {
  double width = MediaQuery.sizeOf(context).width;
  if (width <= 375) {
    return width / 375; // 1.0 at 375px
  } else {
    return 1.0 + (width - 375) / 800;
  }
}
