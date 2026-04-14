import 'package:flutter/material.dart';

LinearGradient buildLinearGradientToAppBarHelper() {
  return const LinearGradient(
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
    colors: [
      Color(0xFFF4F1DE),
      Color(0xFFF6E3EF),
      Color(0xFFEEF4EE),
    ],
  );
}
