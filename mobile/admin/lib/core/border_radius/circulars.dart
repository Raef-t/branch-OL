import 'package:flutter/widgets.dart';

abstract class Circulars {
  static BorderRadius circular1({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.circular(height * 0.002);
  }

  static BorderRadius circular3({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.circular(height * 0.004);
  }

  static BorderRadius circular5({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.circular(height * 0.007);
  }

  static BorderRadius circular6({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.circular(height * 0.009);
  }

  static BorderRadius circular10({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.circular(height * 0.014);
  }

  static BorderRadius circular30({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.circular(height * 0.042);
  }

  static BorderRadius circular63({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.circular(height * 0.09);
  }

  static BorderRadius circular72({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return BorderRadius.circular(height * 0.102);
  }
}
