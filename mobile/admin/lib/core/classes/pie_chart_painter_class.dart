import 'dart:math';
import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

class PieChartPainterClass extends CustomPainter {
  final int completedModules;
  final Color color;

  PieChartPainterClass({required this.completedModules, required this.color});

  @override
  void paint(Canvas canvas, Size size) {
    double completedPercent = completedModules / 100;
    double angleBlue = 2 * pi * completedPercent;
    double angleWhite = 2 * pi * (1 - completedPercent);
    Paint anotherColorPaint = Paint()
      ..style = PaintingStyle.fill
      ..color = color;
    Paint whitePaint = Paint()
      ..style = PaintingStyle.fill
      ..color = ColorsStyle.whiteColor;
    Rect rect = Rect.fromLTWH(0, 0, size.width, size.height);
    canvas.drawArc(rect, -pi / 2, angleBlue, true, anotherColorPaint);
    canvas.drawArc(rect, -pi / 2 + angleBlue, angleWhite, true, whitePaint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => true;
}
