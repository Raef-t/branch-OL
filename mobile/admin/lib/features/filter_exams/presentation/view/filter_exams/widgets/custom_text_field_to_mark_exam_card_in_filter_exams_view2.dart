import 'package:flutter/material.dart';
import '/core/decorations/input_decorations.dart';
import '/core/styles/texts_style.dart';

class CustomTextFieldToMarkExamCardInFilterExamsView2 extends StatelessWidget {
  const CustomTextFieldToMarkExamCardInFilterExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return TextField(
      showCursor: false,
      textAlign: TextAlign.center,
      style: TextsStyle.bold12(context: context),
      decoration:
          InputDecorations.inputDecorationToMarkExamCardInFilterExamsView2(
            context: context,
          ),
    );
  }
}
