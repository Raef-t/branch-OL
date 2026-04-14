import 'package:flutter/material.dart';
import '/core/components/search_text_field_component.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';

class CustomHeaderSectionInSearchView extends StatelessWidget {
  const CustomHeaderSectionInSearchView({super.key});

  @override
  Widget build(BuildContext context) {
    return SymmetricPaddingWithChild.horizontal22(
      context: context,
      child: const SearchTextFieldComponent(),
    );
  }
}
