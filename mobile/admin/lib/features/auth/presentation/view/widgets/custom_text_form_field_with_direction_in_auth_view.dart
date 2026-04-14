import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/features/auth/presentation/view/widgets/custom_text_form_field_in_auth_view.dart';

class CustomTextFormFieldWithDirectionInAuthView extends StatelessWidget {
  const CustomTextFormFieldWithDirectionInAuthView({
    super.key,
    required this.hintText,
    required this.textEditingController,
  });
  final String hintText;
  final TextEditingController textEditingController;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left52(
      context: context,
      child: Directionality(
        textDirection: TextDirection.rtl,
        child: CustomTextFormFieldInAuthView(
          hintText: hintText,
          textEditingController: textEditingController,
        ),
      ),
    );
  }
}
