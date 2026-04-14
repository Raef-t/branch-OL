import 'package:flutter/material.dart';
import '/core/lists/branch_groups_list.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';

PopupMenuItem<String> itemBuilderPopupMenuItemHelper({
  required String text,
  required BuildContext context,
  required bool isSelected,
  required String selectedValue,
}) {
  final bool isFirst = text == branchGroupsList.first;
  // true if this is the first element in the list
  final bool nothingSelected = selectedValue == 'الفرع';
  // color logic: if selected OR first element while user hasn't selected anything yet ('الفرع')
  return PopupMenuItem<String>(
    value: text,
    child: Text(
      text,
      style: TextsStyle.normal10(context: context).copyWith(
        color: (isSelected || (isFirst && nothingSelected))
            ? ColorsStyle.mediumRussetColor2
            : ColorsStyle.mediumBrownColor,
      ),
    ),
  );
}
