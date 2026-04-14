import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/gen/assets.gen.dart';

class CustomIconAndTextInPopupMenuItemHomeView extends StatelessWidget {
  const CustomIconAndTextInPopupMenuItemHomeView({
    super.key,
    required this.selectedValue,
  });
  final String selectedValue;
  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.transparent,
      padding: const EdgeInsets.symmetric(horizontal: 10.0),
      child: Row(
        children: [
          SvgImageComponent(
            pathImage: Assets.images.bottomArrowImage,
            color: ColorsStyle.blackColor,
          ),
          Widths.width8(context: context),
          Text(selectedValue, style: TextsStyle.medium14(context: context)),
        ],
      ),
    );
  }
}
