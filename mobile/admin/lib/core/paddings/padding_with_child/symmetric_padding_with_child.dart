import 'package:flutter/material.dart';

abstract class SymmetricPaddingWithChild {
  static Padding horizontal1({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.symmetric(horizontal: width * 0.002),
      child: child,
    );
  }

  static Padding horizontal10({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.symmetric(horizontal: width * 0.049),
      child: child,
    );
  }

  static Padding horizontal20({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.symmetric(horizontal: width * 0.05),
      child: child,
    );
  }

  static Padding horizontal22({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.symmetric(horizontal: width * 0.054),
      child: child,
    );
  }

  static Padding horizontal30({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.symmetric(horizontal: width * 0.073),
      child: child,
    );
  }

  static Padding vertical5({
    required BuildContext context,
    required Widget child,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.symmetric(vertical: height * 0.007),
      child: child,
    );
  }

  static Padding vertical8({
    required BuildContext context,
    required Widget child,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.symmetric(vertical: height * 0.01),
      child: child,
    );
  }
}
