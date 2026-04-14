import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/auth/presentation/view/widgets/custom_text_form_field_with_direction_in_auth_view.dart';
import '/features/auth/presentation/view/widgets/custom_text_up_field_in_auth_view.dart';

class CustomFieldWithTextUpItInAuthView extends StatelessWidget {
  const CustomFieldWithTextUpItInAuthView({
    super.key,
    required this.text,
    required this.hintText,
    required this.textEditingController,
  });
  final String text, hintText;
  final TextEditingController textEditingController;
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(right: MediaQuery.sizeOf(context).width * 0.09),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          CustomTextUpFieldInAuthView(text: text),
          Heights.height7(context: context),
          CustomTextFormFieldWithDirectionInAuthView(
            hintText: hintText,
            textEditingController: textEditingController,
          ),
        ],
      ),
    );
  }
}
