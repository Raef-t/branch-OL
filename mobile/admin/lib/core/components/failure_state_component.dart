import 'package:flutter/material.dart';
import '/core/components/text_medium13_component.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';
import '/core/styles/colors_style.dart';

class FailureStateComponent extends StatelessWidget {
  const FailureStateComponent({
    super.key,
    required this.errorText,
    this.onPressed,
    this.isAppearIcon = true,
    this.textColor,
  });
  final String errorText;
  final void Function()? onPressed;
  final bool? isAppearIcon;
  final Color? textColor;
  @override
  Widget build(BuildContext context) {
    return SymmetricPaddingWithChild.horizontal10(
      context: context,
      child: Center(
        child: Column(
          children: [
            TextMedium13Component(
              text: errorText,
              color: textColor ?? ColorsStyle.mediumRedColor,
            ),
            if (isAppearIcon!)
              IconButton(
                onPressed: onPressed,
                icon: const Icon(
                  Icons.refresh,
                  color: ColorsStyle.littleVinicColor,
                ),
              ),
          ],
        ),
      ),
    );
  }
}
