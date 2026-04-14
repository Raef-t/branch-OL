import 'package:flutter/material.dart';

abstract class Onlys {
  static BorderRadius topLeft20AndTopRight20AndBottomLeft20({
    required BuildContext context,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.only(
      topLeft: Radius.circular(height * 0.028),
      topRight: Radius.circular(height * 0.028),
      bottomLeft: Radius.circular(height * 0.028),
    );
  }

  static BorderRadius topLeft24AndTopRight24AndBottomRight24({
    required BuildContext context,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.only(
      topLeft: Radius.circular(height * 0.035),
      topRight: Radius.circular(height * 0.035),
      bottomRight: Radius.circular(height * 0.035),
    );
  }

  static BorderRadius topLeft24AndTopRight24AndBottomLeft24({
    required BuildContext context,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.only(
      topLeft: Radius.circular(height * 0.035),
      topRight: Radius.circular(height * 0.035),
      bottomLeft: Radius.circular(height * 0.035),
    );
  }

  static BorderRadius topLeft30AndTopRight30({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.only(
      topLeft: Radius.circular(height * 0.042),
      topRight: Radius.circular(height * 0.042),
    );
  }

  static BorderRadius topLeft55AndBottomLeft55({
    required BuildContext context,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.only(
      topLeft: Radius.circular(height * 0.077),
      bottomLeft: Radius.circular(height * 0.077),
    );
  }

  static BorderRadius topLeft10AndTopRight10AndBottomRight({
    required BuildContext context,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.only(
      topLeft: Radius.circular(height * 0.014),
      topRight: Radius.circular(height * 0.014),
      bottomRight: Radius.circular(height * 0.014),
    );
  }
}
