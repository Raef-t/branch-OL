import 'package:flutter/widgets.dart';

abstract class OnlyPaddingWithChild {
  static Padding bottom5({
    required BuildContext context,
    required Widget child,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.only(bottom: height * 0.007),
      child: child,
    );
  }

  static Padding top6({required BuildContext context, required Widget child}) {
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.only(top: height * 0.009),
      child: child,
    );
  }

  static Padding right8({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.019),
      child: child,
    );
  }

  static Padding right10({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.022),
      child: child,
    );
  }

  static Padding left10({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.022),
      child: child,
    );
  }

  static Padding left19({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.039),
      child: child,
    );
  }

  static Padding bottom14({
    required BuildContext context,
    required Widget child,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.only(bottom: height * 0.02),
      child: child,
    );
  }

  static Padding top15({required BuildContext context, required Widget child}) {
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.only(top: height * 0.021),
      child: child,
    );
  }

  static Padding right15({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.037),
      child: child,
    );
  }

  static Padding right16({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.039),
      child: child,
    );
  }

  static Padding right18({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.043),
      child: child,
    );
  }

  static Padding right20({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.049),
      child: child,
    );
  }

  static Padding right22({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.054),
      child: child,
    );
  }

  static Padding right23({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.056),
      child: child,
    );
  }

  static Padding right25({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.061),
      child: child,
    );
  }

  static Padding right28({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.068),
      child: child,
    );
  }

  static Padding right30({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.073),
      child: child,
    );
  }

  static Padding left30({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.073),
      child: child,
    );
  }

  static Padding right35({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.0845),
      child: child,
    );
  }

  static Padding left38({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.093),
      child: child,
    );
  }

  static Padding right38({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.093),
      child: child,
    );
  }

  static Padding left52({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.126),
      child: child,
    );
  }

  static Padding bottom53({
    required BuildContext context,
    required Widget child,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.only(bottom: height * 0.076),
      child: child,
    );
  }

  static Padding left20AndRight5({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.049, right: width * 0.0125),
      child: child,
    );
  }

  static Padding left18AndRight20({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.049, left: width * 0.044),
      child: child,
    );
  }

  static Padding left19AndRight21({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(right: width * 0.051, left: width * 0.046),
      child: child,
    );
  }

  static Padding left22AndRight21({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.053, right: width * 0.05),
      child: child,
    );
  }

  static Padding left23AndRight21({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.055, right: width * 0.05),
      child: child,
    );
  }

  static Padding left23AndRight22({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.055, right: width * 0.053),
      child: child,
    );
  }

  static Padding left20AndRight22({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.049, right: width * 0.053),
      child: child,
    );
  }

  static Padding left20AndRight22AndBottom42({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(
        left: width * 0.049,
        right: width * 0.053,
        bottom: width * 0.102,
      ),
      child: child,
    );
  }

  static Padding left38AndRight22({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.093, right: width * 0.053),
      child: child,
    );
  }

  static Padding left24AndRight20({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.058, right: width * 0.049),
      child: child,
    );
  }

  static Padding left25AndRight15({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.061, right: width * 0.037),
      child: child,
    );
  }

  static Padding left48AndRight17({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.116, right: width * 0.041),
      child: child,
    );
  }

  static Padding left14AndRight15({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.016, right: width * 0.041),
      child: child,
    );
  }

  static Padding left39AndRight20({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.095, right: width * 0.05),
      child: child,
    );
  }

  static Padding left55AndRight35({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.133, right: width * 0.0845),
      child: child,
    );
  }

  static Padding left18AndRight22AndBottom10({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.only(
        left: width * 0.043,
        right: width * 0.054,
        bottom: height * 0.014,
      ),
      child: child,
    );
  }

  static Padding left18AndRight22AndBottom20({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.only(
        left: width * 0.043,
        right: width * 0.054,
        bottom: height * 0.028,
      ),
      child: child,
    );
  }

  static Padding left21AndRight31AndTop31({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return Padding(
      padding: EdgeInsets.only(
        left: width * 0.051,
        right: width * 0.075,
        top: height * 0.0425,
      ),
      child: child,
    );
  }

  static Padding left26AndRight25({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.062, right: width * 0.06),
      child: child,
    );
  }

  static Padding left35AndRight25({
    required BuildContext context,
    required Widget child,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    return Padding(
      padding: EdgeInsets.only(left: width * 0.085, right: width * 0.061),
      child: child,
    );
  }
}
