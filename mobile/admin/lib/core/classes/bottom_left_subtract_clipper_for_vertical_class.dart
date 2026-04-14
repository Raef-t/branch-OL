import 'package:flutter/material.dart';

class BottomLeftSubtractClipperForVerticalClass extends CustomClipper<Path> {
  final double extraHeight;

  BottomLeftSubtractClipperForVerticalClass({this.extraHeight = 0});

  @override
  Path getClip(Size size) {
    final double width = size.width;
    final double height = size.height;
    final double oldHeight = height - extraHeight;
    final double radius = oldHeight * 0.40;
    final double smoothing = radius * 0.4; // 40% of radius for smoothing

    return Path()
      ..moveTo(0, 0)
      ..lineTo(width, 0)
      ..lineTo(width, height)
      ..lineTo(radius + smoothing, height)
      // Smooth transition from bottom edge to cutout circle
      ..quadraticBezierTo(radius, height, radius, height - smoothing)
      // Main cutout arc (using arcToPoint for simplicity)
      ..arcToPoint(
        Offset(smoothing, height - radius),
        radius: Radius.circular(radius),
        clockwise: false,
      )
      // Smooth transition from cutout circle to left edge
      ..quadraticBezierTo(0, height - radius, 0, height - radius - smoothing)
      ..lineTo(0, 0)
      ..close();
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}
