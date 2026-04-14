import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/text_medium12_component.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class DetailsCardInsideMenuCardComponent extends StatefulWidget {
  const DetailsCardInsideMenuCardComponent({
    super.key,
    required this.text,
    required this.colorToCard,
    required this.colorToText,
  });
  final String text;
  final Color colorToCard, colorToText;

  @override
  State<DetailsCardInsideMenuCardComponent> createState() =>
      _DetailsCardInsideMenuCardComponentState();
}

class _DetailsCardInsideMenuCardComponentState
    extends State<DetailsCardInsideMenuCardComponent> {
  bool isExpanded = false;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final displayText = isExpanded
        ? widget.text
        : (widget.text.length > 10
              ? '${widget.text.substring(0, 5)}...'
              : widget.text);
    return GestureDetector(
      onTap: () => setState(() => isExpanded = !isExpanded),
      child: AnimatedContainer(
        duration: k300MilliSeconds,
        padding:
            OnlyPaddingWithoutChild.left10AndRight10AndTop2halfAndBottom2half(
              context: context,
            ),
        decoration:
            BoxDecorations.boxDecorationToCardsInMenuTabBarWorkHoursView(
              borderRadius: Circulars.circular3(context: context),
              color: widget.colorToCard,
            ),
        child: widget.text == 'مذاكرة' || widget.text == 'كويز'
            ? Row(
                children: [
                  TextMedium12Component(
                    text: displayText,
                    textDirection: TextDirection.rtl,
                    color: widget.colorToText,
                    fontFamily: FontFamily.tajawal,
                  ),
                  Widths.width10(context: context),
                  Container(
                    height: size.height * 0.007,
                    width: size.width * 0.01,
                    decoration: const BoxDecoration(
                      color: ColorsStyle.redColor,
                      shape: BoxShape.circle,
                    ),
                  ),
                ],
              )
            : TextMedium12Component(
                text: displayText,
                textDirection: TextDirection.rtl,
                color: widget.colorToText,
                fontFamily: FontFamily.tajawal,
              ),
      ),
    );
  }
}
