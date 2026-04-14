import 'package:flutter/cupertino.dart';
import '/core/components/text_medium16_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/gen/fonts.gen.dart';

class CustomTextWithPaddingInWorkHoursView extends StatelessWidget {
  const CustomTextWithPaddingInWorkHoursView({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right18(
      context: context,
      child: const Align(
        alignment: Alignment.centerRight,
        child: TextMedium16Component(
          text: 'البرنامج اليومي لجميع الطلاب',
          fontFamily: FontFamily.tajawal,
        ),
      ),
    );
  }
}
