import 'package:flutter/material.dart';
import '/core/components/text_medium13_component.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';

class TextSuccessStateButTheDataIsEmptyComponent extends StatelessWidget {
  const TextSuccessStateButTheDataIsEmptyComponent({
    super.key,
    required this.text,
  });
  final String text;
  @override
  Widget build(BuildContext context) {
    return SymmetricPaddingWithChild.horizontal10(
      context: context,
      child: Center(child: TextMedium13Component(text: text)),
    );
  }
}
