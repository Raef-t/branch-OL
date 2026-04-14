import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/failure_state_component.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/styles/colors_style.dart';

void buildSnackBarToErrorTextHelper({
  required BuildContext context,
  required String errorText,
}) {
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      backgroundColor: ColorsStyle.littleVinicColor,
      shape: RoundedRectangleBorder(
        borderRadius: Circulars.circular10(context: context),
      ),
      behavior: SnackBarBehavior.floating,
      duration: k7Seconds,
      showCloseIcon: true,
      content: FailureStateComponent(
        errorText: errorText,
        isAppearIcon: false,
        textColor: ColorsStyle.whiteColor,
      ),
    ),
  );
}
