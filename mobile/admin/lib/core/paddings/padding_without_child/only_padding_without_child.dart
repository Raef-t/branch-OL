import 'package:flutter/cupertino.dart';

abstract class OnlyPaddingWithoutChild {
  static EdgeInsets bottom9({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(bottom: height * 0.012);
  }

  static EdgeInsets left12({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(left: width * 0.029);
  }

  static EdgeInsets left15AndRight15({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.symmetric(horizontal: width * 0.05);
  }

  static EdgeInsets top15({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(top: height * 0.021);
  }

  static EdgeInsets right15({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(right: width * 0.037);
  }

  static EdgeInsets left15({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(left: width * 0.037);
  }

  static EdgeInsets bottom21({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(bottom: height * 0.031);
  }

  static EdgeInsets left30({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(left: width * 0.073);
  }

  static EdgeInsets left20AndBottom23({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(left: width * 0.048, bottom: height * 0.031);
  }

  static EdgeInsets top15AndBottom10({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(top: height * 0.021, bottom: height * 0.014);
  }

  static EdgeInsets left18AndRight20({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(right: width * 0.049, left: width * 0.044);
  }

  static EdgeInsets left21AndRight20({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(left: width * 0.051, right: width * 0.048);
  }

  static EdgeInsets left24AndRight20({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(left: width * 0.058, right: width * 0.049);
  }

  static EdgeInsets left52AndRight38({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(left: width * 0.126, right: width * 0.093);
  }

  static EdgeInsets left18AndRight22({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(left: width * 0.042, right: width * 0.054);
  }

  static EdgeInsets left37AndRight35({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(left: width * 0.09, right: width * 0.0845);
  }

  static EdgeInsets right20AndLeft20AndBottom21({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      right: width * 0.05,
      left: width * 0.05,
      bottom: height * 0.03,
    );
  }

  static EdgeInsets left18AndRight22AndBottom20({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.043,
      right: width * 0.054,
      bottom: height * 0.028,
    );
  }

  static EdgeInsets left18AndRight22AndBottom8({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.042,
      right: width * 0.054,
      bottom: height * 0.012,
    );
  }

  static EdgeInsets left15AndRight14AndBottom24({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.037,
      right: width * 0.035,
      bottom: height * 0.033,
    );
  }

  static EdgeInsets left18AndRight22AndBottom15({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.042,
      right: width * 0.054,
      bottom: height * 0.021,
    );
  }

  static EdgeInsets left40AndRight39AndBottom15({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.097,
      right: width * 0.095,
      bottom: height * 0.021,
    );
  }

  static EdgeInsets left28AndTop8AndBottom10({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(
      left: width * 0.068,
      bottom: height * 0.014,
      top: height * 0.012,
    );
  }

  static EdgeInsets left15AndTop10AndBottom16({required BuildContext context}) {
    double height = MediaQuery.sizeOf(context).height;
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(
      left: width * 0.037,
      bottom: height * 0.023,
      top: height * 0.014,
    );
  }

  static EdgeInsets left20AndRight20AndBottom8({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.048,
      right: width * 0.048,
      bottom: height * 0.012,
    );
  }

  static EdgeInsets left20AndRight20AndBottom14({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.048,
      right: width * 0.048,
      bottom: height * 0.019,
    );
  }

  static EdgeInsets left11AndTop8AndBottom7({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.027,
      top: height * 0.0115,
      bottom: height * 0.01,
    );
  }

  static EdgeInsets top9AndBottom4AndLeft13({required BuildContext context}) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      top: height * 0.012,
      left: width * 0.033,
      bottom: height * 0.005,
    );
  }

  static EdgeInsets left37AndRight18AndBottom15({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.089,
      right: width * 0.043,
      bottom: height * 0.021,
    );
  }

  static EdgeInsets left19AndTop13AndRight7AndBottom2({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.046,
      top: height * 0.019,
      right: width * 0.017,
      bottom: height * 0.004,
    );
  }

  static EdgeInsets left9AndRight10AndTop14AndBottom14({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.022,
      right: width * 0.025,
      top: height * 0.021,
      bottom: height * 0.021,
    );
  }

  static EdgeInsets top13AndBottom13AndRight17AndLeft28({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      top: height * 0.02,
      right: width * 0.041,
      left: width * 0.069,
      bottom: height * 0.02,
    );
  }

  static EdgeInsets top15AndBottom10AndRight10AndLeft10({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      top: height * 0.021,
      bottom: height * 0.014,
      right: width * 0.01,
      left: width * 0.01,
    );
  }

  static EdgeInsets top15AndBottom10AndRight18AndLeft18({
    required BuildContext context,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(
      top: height * 0.021,
      bottom: height * 0.014,
      left: width * 0.042,
      right: width * 0.042,
    );
  }

  static EdgeInsets top8AndBottom4AndRight14AndLeft14({
    required BuildContext context,
  }) {
    double height = MediaQuery.sizeOf(context).height;
    double width = MediaQuery.sizeOf(context).width;
    return EdgeInsets.only(
      top: height * 0.011,
      bottom: height * 0.005,
      right: width * 0.035,
      left: width * 0.035,
    );
  }

  static EdgeInsets left22AndTop14AndRight31AndBottom15({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.053,
      top: height * 0.019,
      right: width * 0.075,
      bottom: height * 0.021,
    );
  }

  static EdgeInsets left10AndRight10AndTop2halfAndBottom2half({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.024,
      right: width * 0.024,
      top: height * 0.0035,
      bottom: height * 0.0035,
    );
  }

  static EdgeInsets left33AndRight32AndTop45AndBottom45({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      top: height * 0.065,
      bottom: height * 0.065,
      right: width * 0.077,
      left: width * 0.079,
    );
  }

  static EdgeInsets left14AndTop10AndRight8AndBottom13({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.034,
      top: height * 0.014,
      right: width * 0.019,
      bottom: height * 0.02,
    );
  }

  static EdgeInsets left26AndRight44AndTop10AndBottom11({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      left: width * 0.063,
      right: width * 0.108,
      top: height * 0.014,
      bottom: height * 0.016,
    );
  }

  static EdgeInsets top12AndBottom12AndLeft35AndRight34({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      top: height * 0.018,
      bottom: height * 0.018,
      left: width * 0.085,
      right: width * 0.083,
    );
  }

  static EdgeInsets left17AndTop12AndBottom29AndRight39({
    required BuildContext context,
  }) {
    double width = MediaQuery.sizeOf(context).width;
    double height = MediaQuery.sizeOf(context).height;
    return EdgeInsets.only(
      top: height * 0.018,
      right: width * 0.095,
      left: width * 0.041,
      bottom: height * 0.042,
    );
  }
}
