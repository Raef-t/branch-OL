import 'package:flutter/material.dart';
import '/core/components/filter_card_and_search_field_component.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';
import '/gen/assets.gen.dart';

class CustomHeaderInExamsView2 extends StatelessWidget {
  const CustomHeaderInExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return SymmetricPaddingWithChild.horizontal22(
      context: context,
      child: FilterCardAndSearchFieldComponent(
        imageProvider: Assets.images.blueFilterImage.provider(),
      ),
    );
  }
}
