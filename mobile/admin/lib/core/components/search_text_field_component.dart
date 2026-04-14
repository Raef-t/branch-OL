import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/decorations/input_decorations.dart';
import '/core/styles/texts_style.dart';

class SearchTextFieldComponent extends StatelessWidget {
  const SearchTextFieldComponent({super.key, this.readOnly, this.onTap});
  final bool? readOnly;
  final void Function()? onTap;
  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: Circulars.circular10(context: context),
      child: TextField(
        readOnly: readOnly ?? false,
        onTap: onTap,
        textDirection: TextDirection.rtl,
        textAlignVertical: TextAlignVertical.center,
        style: TextsStyle.bold14(context: context),
        decoration: InputDecorations.inputDecorationSearchTextFieldComponent(
          context: context,
        ),
      ),
    );
  }
}
