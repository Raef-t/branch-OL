import 'package:flutter/material.dart';
import '/core/components/text_medium18_component.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/paddings/padding_without_child/symmetric_padding_without_child.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomRegisterCardButtonInAuthView extends StatelessWidget {
  const CustomRegisterCardButtonInAuthView({super.key, required this.onTap});
  final void Function() onTap;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: double.infinity,
        alignment: Alignment.center,
        padding: SymmetricPaddingWithoutChild.vertical9(context: context),
        margin: OnlyPaddingWithoutChild.left52AndRight38(context: context),
        decoration: BoxDecorations.boxDecorationToRegisterCardButtonInAuthView(
          context: context,
        ),
        child: const TextMedium18Component(
          text: 'تسجيل الدخول',
          color: ColorsStyle.littleGreyColor,
          fontFamily: FontFamily.tajawal,
        ),
      ),
    );
  }
}
