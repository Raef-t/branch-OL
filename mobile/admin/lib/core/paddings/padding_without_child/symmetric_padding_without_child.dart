import 'package:flutter/material.dart';

abstract class SymmetricPaddingWithoutChild {
  static EdgeInsets vertical5({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.symmetric(vertical: height * 0.007);
  }

  static EdgeInsets vertical9({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.symmetric(vertical: height * 0.012);
  }

  static EdgeInsets horizontal15({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.symmetric(horizontal: width * 0.037);
  }

  static EdgeInsets horizontal20({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.symmetric(horizontal: width * 0.048);
  }

  static EdgeInsets horizontal9AndVertical3({required BuildContext context}) {
    Size size = MediaQuery.sizeOf(context);
    return EdgeInsets.symmetric(
      horizontal: size.width * 0.023,
      vertical: size.height * 0.004,
    );
  }

  static EdgeInsets horizontal27AndVertical9({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.symmetric(
      horizontal: width * 0.066,
      vertical: height * 0.013,
    );
  }

  static EdgeInsets horizontal35AndVertical45({required BuildContext context}) {
    Size size = MediaQuery.sizeOf(context);
    return EdgeInsets.symmetric(
      horizontal: size.width * 0.051,
      vertical: size.height * 0.12,
    );
  }
}
