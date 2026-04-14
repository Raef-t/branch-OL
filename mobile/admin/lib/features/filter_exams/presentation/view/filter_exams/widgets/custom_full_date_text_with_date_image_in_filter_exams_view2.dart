import 'package:flutter/material.dart';
import '/core/components/text_arabic_full_date_with_chevron_icons_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/gen/assets.gen.dart';

class CustomFullDateTextWithDateImageInFilterExamsView2
    extends StatelessWidget {
  const CustomFullDateTextWithDateImageInFilterExamsView2({
    super.key,
    this.previousWeek,
    this.nextWeek,
    this.goToCurrentWeek,
  });
  final VoidCallback? previousWeek, nextWeek, goToCurrentWeek;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left23AndRight22(
      context: context,
      child: Row(
        children: [
          TextArabicFullDateWithChevronIconsComponent(
            leftOnPressed: previousWeek,
            rightOnPressed: nextWeek,
            goToCurrentWeek: goToCurrentWeek,
          ),
          const Spacer(),
          Assets.images.dateImage.image(),
        ],
      ),
    );
  }
}
