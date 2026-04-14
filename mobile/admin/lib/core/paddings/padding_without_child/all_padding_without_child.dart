import 'package:flutter/material.dart';

abstract class AllPaddingWithoutChild {
  static EdgeInsets all10({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.all(height * 0.014);
  }
}
