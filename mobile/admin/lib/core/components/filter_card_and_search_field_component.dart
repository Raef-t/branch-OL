import 'package:flutter/material.dart';
import '/core/components/filter_card_component.dart';
import '/core/components/search_text_field_component.dart';
import '/core/sized_boxs/widths.dart';

class FilterCardAndSearchFieldComponent extends StatelessWidget {
  const FilterCardAndSearchFieldComponent({
    super.key,
    this.readOnly,
    this.onTap,
    required this.imageProvider,
  });
  final bool? readOnly;
  final void Function()? onTap;
  final ImageProvider imageProvider;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        FilterCardComponent(imageProvider: imageProvider),
        Widths.width19(context: context),
        Expanded(
          child: SearchTextFieldComponent(readOnly: readOnly, onTap: onTap),
        ),
      ],
    );
  }
}
