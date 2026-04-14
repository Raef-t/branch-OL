import 'package:flutter/material.dart';
import '/core/helpers/build_outline_input_border_to_field_in_auth_view_helper.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/gen/fonts.gen.dart';

class CustomTextFormFieldInAuthView extends StatelessWidget {
  const CustomTextFormFieldInAuthView({
    super.key,
    required this.hintText,
    required this.textEditingController,
  });
  final String hintText;
  final TextEditingController textEditingController;
  @override
  Widget build(BuildContext context) {
    return Material(
      child: TextFormField(
        controller: textEditingController,
        validator: (value) {
          if (value == null || value.isEmpty) {
            return 'هذا الحقل مطلوب';
          }
          return null;
        },
        style: TextsStyle.bold12(
          context: context,
        ).copyWith(color: ColorsStyle.greyColor),
        decoration: InputDecoration(
          fillColor: ColorsStyle.whiteColor,
          filled: true,
          // isDense: true,
          // contentPadding: const EdgeInsets.symmetric(
          //   vertical: 12,
          //   horizontal: 8,
          // ),
          hintText: hintText,
          hintStyle: TextsStyle.medium12(context: context).copyWith(
            fontFamily: FontFamily.tajawal,
            color: ColorsStyle.greyColor,
          ),
          border: buildOutlineInputBorderToFieldInAuthViewHelper(
            context: context,
          ),
          enabledBorder: buildOutlineInputBorderToFieldInAuthViewHelper(
            context: context,
          ),
          focusedBorder: buildOutlineInputBorderToFieldInAuthViewHelper(
            context: context,
          ),
        ),
      ),
    );
  }
}
