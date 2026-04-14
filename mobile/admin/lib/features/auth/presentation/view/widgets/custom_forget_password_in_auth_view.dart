import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomForgetPasswordInAuthView extends StatelessWidget {
  const CustomForgetPasswordInAuthView({
    super.key,
    required this.isChecked,
    required this.onChanged,
  });
  final bool isChecked;
  final void Function(bool?)? onChanged;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left52(
      context: context,
      child: Row(
        // mainAxisAlignment: MainAxisAlignment.start,
        children: [
          const Flexible(
            child: TextMedium12Component(
              text: 'نسيت كلمة المرور',
              fontFamily: FontFamily.tajawal,
              color: ColorsStyle.greyColor,
              textDirection: TextDirection.rtl,
            ),
          ),
          const SizedBox(width: 8),
          Transform.scale(
            scale: 0.8,
            child: Checkbox(
              value: isChecked,
              onChanged: onChanged,
              materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
              visualDensity: VisualDensity.compact,
            ),
          ),
        ],
      ),
    );
  }
}
