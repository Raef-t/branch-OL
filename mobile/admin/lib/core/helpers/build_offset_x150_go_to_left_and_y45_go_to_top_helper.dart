import 'package:flutter/material.dart';

Offset buildOffsetX150GoToLeftAndY45GoToTopHelper({
  required BuildContext context,
}) {
  Size size = MediaQuery.sizeOf(context);
  return Offset(-size.width * 0.28, -size.height * 0.075);
}
