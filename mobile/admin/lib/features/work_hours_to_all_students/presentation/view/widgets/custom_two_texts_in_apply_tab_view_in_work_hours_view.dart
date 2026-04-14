import 'package:flutter/material.dart';
import '/core/components/text_bold13_component.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';

class CustomTwoTextsInApplyTabViewInWorkHoursView extends StatelessWidget {
  const CustomTwoTextsInApplyTabViewInWorkHoursView({
    super.key,
    required this.text,
  });
  final String text;
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: OnlyPaddingWithoutChild.left28AndTop8AndBottom10(
        context: context,
      ),
      child: Align(
        alignment: Alignment.centerLeft,
        child: Column(
          children: [
            TextBold13Component(text: text),
            const TextBold13Component(text: 'AM'),
          ],
        ),
      ),
    );
  }
}
