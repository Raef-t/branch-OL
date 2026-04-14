import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/styles/colors_style.dart';

OutlineInputBorder buildOutlineInputBorderToFieldInAuthViewHelper({
  required BuildContext context,
}) {
  return OutlineInputBorder(
    borderRadius: Circulars.circular10(context: context),
    borderSide: const BorderSide(color: ColorsStyle.mediumGreyColor),
  );
}
