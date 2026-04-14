import 'package:flutter/material.dart';
import '/core/components/text_medium18_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/gen/fonts.gen.dart';

class CustomHeaderInRatingCardInDetailsStudentView extends StatelessWidget {
  const CustomHeaderInRatingCardInDetailsStudentView({
    super.key,
    required this.selectedValue,
    required this.onSelected,
  });
  final String selectedValue;
  final void Function(String) onSelected;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right15(
      context: context,
      child: const Align(
        alignment: Alignment.centerRight,
        child: TextMedium18Component(
          text: 'تقييم الطالب',
          fontFamily: FontFamily.tajawal,
        ),
      ),
      // child: Row(
      //   children: [
      //     CustomPopupMenuInDetailsStudentView(
      //       selectedValue: selectedValue,
      //       onSelected: onSelected,
      //     ),
      //     const Spacer(),
      //     const TextMedium18Component(
      //       text: 'تقييم الطالب',
      //       fontFamily: FontFamily.tajawal,
      //     ),
      //   ],
      // ),
    );
  }
}
