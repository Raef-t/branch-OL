import 'package:flutter/material.dart';

Offset buildOffsetY1GoToBottomAndX0Helper({required BuildContext context}) {
  double height = MediaQuery.sizeOf(context).height;
  return Offset(0, height * 0.002);
}
