import 'package:flutter/material.dart';
import '/core/components/search_text_field_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';

class CustomHeaderSectionInCoursesView extends StatelessWidget {
  const CustomHeaderSectionInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left24AndRight20(
      context: context,
      child: const SearchTextFieldComponent(),
    );
  }
}
