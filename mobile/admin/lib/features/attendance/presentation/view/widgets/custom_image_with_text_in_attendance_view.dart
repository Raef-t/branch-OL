import 'package:flutter/cupertino.dart';
import '/core/components/text_medium16_component.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomImageWithTextInAttendanceView extends StatelessWidget {
  const CustomImageWithTextInAttendanceView({super.key});

  @override
  Widget build(BuildContext context) {
    return SymmetricPaddingWithChild.horizontal20(
      context: context,
      child: Row(
        children: [
          Assets.images.bigDateImage.image(),
          const Spacer(),
          const TextMedium16Component(
            text: 'الاسبوع الحالي',
            fontFamily: FontFamily.tajawal,
            color: ColorsStyle.mediumBlackColor2,
          ),
        ],
      ),
    );
  }
}
