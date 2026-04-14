import 'package:flutter/material.dart';
import '/core/components/text_normal14_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';

class TextWithImageInScanOrDisplayQRMessageComponent extends StatelessWidget {
  const TextWithImageInScanOrDisplayQRMessageComponent({
    super.key,
    required this.text,
    required this.image,
    required this.onTap,
  });
  final String text;
  final Image image;
  final void Function() onTap;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          TextNormal14Component(
            text: text,
            color: ColorsStyle.mediumBlackColor2,
          ),
          Widths.width8(context: context),
          image,
        ],
      ),
    );
  }
}
