import 'package:flutter/material.dart';
import '/core/components/text_medium15_component.dart';

PopupMenuItem<String> buildPopupMenuItemInDetailsStudentViewHelper({
  required String text,
}) {
  return PopupMenuItem<String>(
    value: text,
    child: TextMedium15Component(text: text),
  );
}
