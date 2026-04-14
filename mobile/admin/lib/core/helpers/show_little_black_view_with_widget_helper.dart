import 'package:flutter/material.dart';
import '/core/components/small_black_view_component.dart';

void showLittleBlackViewWithWidgetHelper({
  required BuildContext context,
  required Widget widget,
}) {
  showDialog(
    context: context,
    builder: (context) {
      return Stack(children: [const LittleBlackViewComponent(), widget]);
    },
  );
}
